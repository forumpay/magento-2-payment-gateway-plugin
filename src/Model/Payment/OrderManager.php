<?php

namespace ForumPay\PaymentGateway\Model\Payment;

use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\TransactionFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use Magento\Sales\Model\Spi\TransactionResourceInterface;
use ForumPay\PaymentGateway\PHPClient\Response\CheckPaymentResponse;
use ForumPay\PaymentGateway\PHPClient\Response\StartPaymentResponse;
use ForumPay\PaymentGateway\Exception\ForumPayException;

/**
 * Manages internal states of the order and provides and interface for dealing with Magento internal
 */
class OrderManager
{
    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @var Transaction\BuilderInterface
     */
    private Transaction\BuilderInterface $transactionBuilder;

    /**
     * @var OrderResourceInterface
     */
    private OrderResourceInterface $orderResourceModel;

    /**
     * @var Payment
     */
    private Payment $paymentResourceModel;

    /**
     * @var TransactionResourceInterface
     */
    private TransactionResourceInterface $transactionResourceModel;

    /**
     * @var TransactionFactory
     */
    private TransactionFactory $transactionFactory;

    /**
     * @var OrderFactory
     */
    private OrderFactory $orderFactory;

    /**
     * @var Order
     */
    private Order $orderModel;

    /**
     * Order Manager constructor
     *
     * @param Session $checkoutSession
     * @param OrderResourceInterface $orderResourceModel
     * @param Payment $paymentResourceModel
     * @param TransactionResourceInterface $transactionResourceModel
     * @param Transaction\BuilderInterface $transactionBuilder
     * @param TransactionFactory $transactionFactory
     * @param OrderFactory $orderFactory
     * @param Order $orderModel
     */
    public function __construct(
        Session $checkoutSession,
        OrderResourceInterface $orderResourceModel,
        Payment $paymentResourceModel,
        TransactionResourceInterface $transactionResourceModel,
        Transaction\BuilderInterface $transactionBuilder,
        TransactionFactory $transactionFactory,
        OrderFactory $orderFactory,
        \Magento\Sales\Model\Order $orderModel
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderResourceModel = $orderResourceModel;
        $this->paymentResourceModel = $paymentResourceModel;
        $this->transactionResourceModel = $transactionResourceModel;
        $this->transactionBuilder = $transactionBuilder;
        $this->orderFactory = $orderFactory;
        $this->transactionFactory = $transactionFactory;
        $this->orderModel = $orderModel;
    }

    /**
     * Return current session quote
     *
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote(): Quote
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Update current order with a given status
     *
     * @param Order $order
     * @param string $newOrderStatus
     * @return Order
     * @throws \Exception
     */
    public function updateOrderStatus(Order $order, string $newOrderStatus): Order
    {
        $order->setStatus($newOrderStatus
            ? $newOrderStatus
            : Order::STATE_PENDING_PAYMENT);

        $this->orderResourceModel->save($order);

        return $order;
    }

    /**
     * Update order with the payment information from ForumPay
     *
     * @param Order $order
     * @param CheckPaymentResponse|StartPaymentResponse $forumPayPaymentInfo
     * @param string $paymentId
     * @param string|null $orderStatusAfterPayment
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function savePaymentDataToOrder(
        Order $order,
        $forumPayPaymentInfo,
        string $paymentId,
        ?string $orderStatusAfterPayment = Order::STATE_PROCESSING
    ) {
        $newOrderStatus = null;

        $formattedPrice = $order->getBaseCurrency()->formatTxt(
            $order->getGrandTotal()
        );

        $txnType = TransactionInterface::TYPE_AUTH;
        $isClosed = false;
        $message = __('Initialize Payment amount is %1.', $formattedPrice);

        if ($forumPayPaymentInfo instanceof  CheckPaymentResponse) {
            if (strtolower($forumPayPaymentInfo->getStatus())  === 'cancelled') {
                $newOrderStatus = Order::STATE_CANCELED;

                $txnType = TransactionInterface::TYPE_VOID;
                $message = __('Payment Failed amount is %1.', $formattedPrice);
                $isClosed = true;
            } elseif (strtolower($forumPayPaymentInfo->getStatus()) === 'confirmed') {
                $newOrderStatus = $orderStatusAfterPayment;

                $txnType = TransactionInterface::TYPE_CAPTURE;
                $message = __('The Captured Payment amount is %1.', $formattedPrice);
                $isClosed = true;
            }
        }

        $transactionDetails =
            $this->getTransactionAdditionalInformation($paymentId);

        try {
            $transaction = $this->getTransaction($paymentId);
        } catch (\Exception $e) {
            $transaction = null;
        }
        if ($transaction && $transaction->getIsClosed()) {
            return;
        }

        /** @var Order\Payment $payment */
        $payment = $order->getPayment();
        $payment->setLastTransId($paymentId);
        $payment->setTransactionId($paymentId);
        $payment->setIsTransactionClosed($isClosed);
        $payment->setAdditionalInformation(
            [Transaction::RAW_DETAILS => array_merge(
                $this->convertAllArrayValuesToString($forumPayPaymentInfo->toArray()),
                $transactionDetails
            )]
        );

        $transaction = $this->transactionBuilder
            ->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($paymentId ? :$forumPayPaymentInfo->getPaymentId())
            ->setAdditionalInformation(
                [Transaction::RAW_DETAILS => array_merge(
                    $this->convertAllArrayValuesToString($forumPayPaymentInfo->toArray()),
                    $transactionDetails
                )]
            )->setFailSafe(false)
            ->build($txnType);

        $payment->addTransactionCommentsToOrder(
            $transaction,
            $message
        );

        $payment->setParentTransactionId($paymentId ? :$forumPayPaymentInfo->getPaymentId());
        $this->paymentResourceModel->save($payment);

        if ($newOrderStatus) {
            $order->setStatus($newOrderStatus);
        }

        $this->orderResourceModel->save($order);
    }

    /**
     * Get order by ForumPay paymentId
     *
     * @param string $paymentId
     * @return Order
     * @throws ForumPayException
     */
    public function getOrderByPaymentId(string $paymentId): Order
    {
        $order = $this->orderModel->load(
            $this->getTransaction($paymentId)->getOrderId()
        );

        if (!$order || !$order->getId()) {
            throw new ForumPayException(__('Order for specified payment id not found'));
        }

        return $order;
    }

    /**
     * Return current order currency code.
     *
     * @return string
     * @throws ForumPayException
     */
    public function getCurrentOrderCurrencyCode(): string
    {
        return $this->getCurrentOrder()->getOrderCurrencyCode();
    }

    /**
     * Return current order GrandTotal value formatted to 2 decimal palaces.
     *
     * @return string
     * @throws ForumPayException
     */
    public function getBaseGrandTotal(): string
    {
        return number_format($this->getCurrentOrder()->getBaseGrandTotal(), 2);
    }

    /**
     * Get ForumPay payment details saved to order transaction  that have been saved by StartPayment method.
     *
     * @param string $paymentId
     * @return array
     */
    public function getTransactionAdditionalInformation(string $paymentId): array
    {
        try {
            $transaction = $this->getTransaction($paymentId);
            return $transaction->getAdditionalInformation(Transaction::RAW_DETAILS);
        } catch (ForumPayException $e) {
            return [];
        }
    }

    /**
     * Get order from current magento session
     *
     * @return Order
     * @throws ForumPayException
     */
    public function getCurrentOrder(): Order
    {
        $order = $this->checkoutSession->getLastRealOrder();

        if (!$order) {
            throw new ForumPayException(__('Order for current session was not found.'));
        }

        return $order;
    }

    /**
     * Returns transaction by transactionId
     *
     * @param string $transactionId
     * @return TransactionInterface
     * @throws ForumPayException
     */
    public function getTransaction(string $transactionId): TransactionInterface
    {
        $transaction = $this->transactionFactory->create();
        $this->transactionResourceModel->load(
            $transaction,
            $transactionId,
            TransactionInterface::TXN_ID
        );

        if (!$transaction || $transaction->getId() === null) {
            throw new ForumPayException(__('Transaction not found.'));
        }

        return $transaction;
    }

    /**
     * Returns last order items in shopping cart
     */
    public function restoreCart(): void
    {
        $this->checkoutSession->restoreQuote();
    }

    /**
     * Covert any value of the array to string so that it can be easily serialized
     *
     * @param array $array
     * @return array
     */
    private function convertAllArrayValuesToString(array $array): array
    {
        return array_map(function ($item) {
            if (is_array($item)) {
                return json_encode($item);
            }

            if (is_object($item) && method_exists($item, '__toString')) {
                return (string)$item;
            }

            if (settype($item, 'string') !== false) {
                return (string)$item;
            }

            return 'Data can not be converted to string';
        }, $array);
    }
}
