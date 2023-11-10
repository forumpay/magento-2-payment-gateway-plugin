<?php

namespace ForumPay\PaymentGateway\Model\Payment;

use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Exception\QuoteIsNotActiveException;
use ForumPay\PaymentGateway\Exception\TransactionDetailsMissingException;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\RequestKycResponse;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentData;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Model\Quote;
use ForumPay\PaymentGateway\PHPClient\PaymentGatewayApi;
use ForumPay\PaymentGateway\PHPClient\PaymentGatewayApiInterface;
use ForumPay\PaymentGateway\PHPClient\Response\CheckPaymentResponse;
use ForumPay\PaymentGateway\PHPClient\Response\GetCurrencyListResponse;
use ForumPay\PaymentGateway\PHPClient\Response\GetRateResponse;
use ForumPay\PaymentGateway\Helper\Data;
use ForumPay\PaymentGateway\PHPClient\Response\StartPaymentResponse;

/**
 * ForumPay payment method model
 */
class ForumPay extends \Magento\Payment\Model\Method\AbstractMethod
{
    public const PAYMENT_METHOD_CODE = 'forumpay';

    /**
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isOffline = false;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var PaymentGatewayApiInterface
     */
    private PaymentGatewayApiInterface $apiClient;

    /**
     * @var Data
     */
    private Data $forumPayConfig;

    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private \Psr\Log\LoggerInterface $psrLogger;

    /**
     * ForumPay payment method constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param PaymentData $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param Data $forumPayConfig
     * @param OrderManager $orderManager
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param ForumPayLogger $forumPayLogger
     */
    public function __construct(
        Context                    $context,
        Registry                   $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory      $customAttributeFactory,
        PaymentData                $paymentData,
        ScopeConfigInterface       $scopeConfig,
        Logger                     $logger,
        Data                       $forumPayConfig,
        OrderManager               $orderManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        ForumPayLogger $forumPayLogger
    ) {
        $this->apiClient = new PaymentGatewayApi(
            $forumPayConfig->getApiUrl(),
            $forumPayConfig->getMerchantApiUser(),
            $forumPayConfig->getMerchantApiSecret(),
            sprintf(
                "fp-pgw[%s] %s %s %s on PHP %s",
                $forumPayConfig->getVersion(),
                $productMetadata->getName(),
                $productMetadata->getEdition(),
                $productMetadata->getVersion(),
                phpversion()
            ),
            $forumPayConfig->getStoreLocale(),
            null,
            $forumPayLogger
        );

        $this->forumPayConfig = $forumPayConfig;
        $this->orderManager = $orderManager;
        $this->psrLogger = $forumPayLogger;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );
    }

    /**
     * This method initiates a KYC request by sending an email to request a KYC via an API call.
     *
     * @return RequestKycResponse
     * @throws ApiExceptionInterface
     * @throws ForumPayException
     */
    public function requestKyc(): RequestKycResponse
    {
        return $this->apiClient->requestKyc($this->orderManager->getOrderCustomerEmail());
    }

    /**
     * Get list of available cryptocurrencies from ForumPay for current active quote FIAT currency
     *
     * @return GetCurrencyListResponse
     * @throws ForumPayException
     */
    public function getCryptoCurrencyList(): GetCurrencyListResponse
    {
        $quote = $this->getCurrentQuote();
        $currency = $quote->getQuoteCurrencyCode();

        if (empty($currency)) {
            throw new ForumPayException(
                __('Store currency could not be determined')
            );
        }

        return $this->apiClient->getCurrencyList($currency);
    }

    /**
     * Get current rate information from ForumPay
     *
     * @param string $currency
     * @return GetRateResponse
     * @throws ForumPayException
     */
    public function getRate(string $currency): GetRateResponse
    {
        try {
            $quote = $this->getCurrentQuote();
            if (!$quote->getIsActive()) {
                throw new QuoteIsNotActiveException(__("Quote is not active. Order is already created."));
            }
        } catch (ForumPayException $e) {
            throw new QuoteIsNotActiveException(__("Quote is not active. Order is already created."));
        }

        return $this->apiClient->getRate(
            $this->forumPayConfig->getPosId(),
            $quote->getQuoteCurrencyCode(),
            number_format($quote->getGrandTotal(), 2, '.', ''),
            $currency,
            $this->forumPayConfig->isAcceptZeroConfirmations() ? 'true' : 'false',
            null,
            null,
            null
        );
    }

    /**
     * Start payment for the current order.
     *
     * @param string $currency
     * @param string $paymentId
     * @param string|null $kycPin
     * @return StartPaymentResponse
     * @throws ForumPayException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function startPayment(string $currency, string $paymentId, ?string $kycPin): StartPaymentResponse
    {
        $order = $this->orderManager->getCurrentOrder();
        $this->orderManager->updateOrderStatus(
            $order,
            $this->forumPayConfig->getNewOrderStatus()
        );

        $response = $this->apiClient->startPayment(
            $this->forumPayConfig->getPosId(),
            $order->getOrderCurrencyCode(),
            $paymentId,
            $this->orderManager->getBaseGrandTotal(),
            $currency,
            $order->getIncrementId(),
            $this->forumPayConfig->isAcceptZeroConfirmations() ? 'true' : 'false',
            $this->getOrderRemoteIp($order),
            $order->getCustomerEmail(),
            $order->getCustomerIsGuest() ? 'guest-order-id_' . $order->getId() : 'user-id_' . $order->getCustomerId(),
            false,
            false,
            false,
            null,
            null,
            null,
            null,
            null,
            $kycPin
        );

        $this->orderManager->savePaymentDataToOrder(
            $order,
            $response,
            $paymentId,
            $this->forumPayConfig->getOrderStatusAfterPayment()
        );

        return $response;
    }

    /**
     * Get detailed payment information for ForumPay
     *
     * @param string $paymentId
     * @return CheckPaymentResponse
     * @throws ForumPayException
     */
    public function checkPayment(string $paymentId): CheckPaymentResponse
    {
        $transactionDetails = $this->orderManager->getTransactionAdditionalInformation($paymentId);

        if (!array_key_exists('address', $transactionDetails)) {
            throw new TransactionDetailsMissingException(
                __("Transaction not found by transaction ID. Order already processed.")
            );
        }

        $response = $this->apiClient->checkPayment(
            $this->forumPayConfig->getPosId(),
            $transactionDetails['currency'],
            $paymentId,
            $transactionDetails['address']
        );

        if (strtolower($response->getStatus()) == 'cancelled' || strtolower($response->getStatus()) == 'confirmed') {
            $this->orderManager->savePaymentDataToOrder(
                $this->orderManager->getOrderByPaymentId($paymentId),
                $response,
                $paymentId,
                $this->forumPayConfig->getOrderStatusAfterPayment()
            );
        }

        if (strtolower($response->getStatus()) == 'cancelled') {
            $this->orderManager->restoreCart();
        }

        return $response;
    }

    /**
     * Cancel the payment on ForumPay
     *
     * @param string $paymentId
     * @param string $reason
     * @param string $description
     */
    public function cancelPayment(string $paymentId, string $reason = '', string $description = '')
    {
        $transactionDetails = $this->orderManager->getTransactionAdditionalInformation($paymentId);

        $response = $this->apiClient->cancelPayment(
            $this->forumPayConfig->getPosId(),
            $transactionDetails['currency'],
            $paymentId,
            $transactionDetails['address'],
            $reason,
            $description,
        );

        if ($response->isCancelled() && strtolower($response->getStatus()) == 'cancelled') {
            $this->orderManager->restoreCart();
        }
    }

    /**
     * Returns last order items in shopping cart
     *
     * @return void
     */
    public function restoreCart(): void
    {
        $this->orderManager->restoreCart();
    }

    /**
     * Get current quote
     *
     * @return Quote
     * @throws ForumPayException
     */
    private function getCurrentQuote(): Quote
    {
        $quote = $this->orderManager->getQuote();

        if (!$quote || $quote->getId() === null) {
            throw new ForumPayException(
                __('Forumpay payment method must be used after the Quote is generated.')
            );
        }

        return $quote;
    }

    /**
     * Combines all available IP address and try to filter out a public one.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     * @throws ForumPayException
     */
    private function getOrderRemoteIp(\Magento\Sales\Model\Order $order): string
    {
        $remoteAddressesList =
            preg_split("/,/", $order->getRemoteIp(), -1, PREG_SPLIT_NO_EMPTY)
            + preg_split("/,/", $order->getXForwardedFor() ?? '', -1, PREG_SPLIT_NO_EMPTY);

        if (!count($remoteAddressesList)) {
            throw new ForumPayException(__('You IP address could not be determined'));
        }

        foreach ($remoteAddressesList as $remoteAddress) {
            if (filter_var(
                $remoteAddress,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )) {
                return $remoteAddress;
            }
        }

        return $remoteAddressesList[0];
    }
}
