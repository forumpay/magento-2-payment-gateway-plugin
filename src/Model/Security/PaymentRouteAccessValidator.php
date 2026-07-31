<?php

namespace ForumPay\PaymentGateway\Model\Security;

use ForumPay\PaymentGateway\Api\Security\PaymentRouteAccessValidatorInterface;
use ForumPay\PaymentGateway\Model\Payment\OrderManager;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Webapi\Exception as WebapiException;

class PaymentRouteAccessValidator implements PaymentRouteAccessValidatorInterface
{
    private const ACCESS_DENIED_CODE = 4003;

    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @param OrderManager $orderManager
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        OrderManager $orderManager,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession
    ) {
        $this->orderManager = $orderManager;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritdoc
     */
    public function assertActiveCheckoutQuote(): void
    {
        $sessionQuoteId = (int) $this->checkoutSession->getQuoteId();
        $sessionOrderId = (int) $this->checkoutSession->getLastOrderId();

        if ($sessionQuoteId <= 0 && $sessionOrderId <= 0) {
            $this->denyAccess();
        }

        try {
            $quote = $this->orderManager->getQuote();
        } catch (\Exception $e) {
            $this->denyAccess();
            return;
        }

        $quoteId = (int) $quote->getId();

        if ($quoteId <= 0) {
            $this->denyAccess();
        }

        if ((int) $quote->getItemsCount() <= 0) {
            $this->denyAccess();
        }

        if ($sessionQuoteId > 0 && $quoteId === $sessionQuoteId) {
            return;
        }

        if ($sessionOrderId > 0) {
            $order = null;
            try {
                $order = $this->orderManager->getCurrentOrder();
            } catch (\Exception $e) {
                $order = null;
            }

            if ($order !== null
                && (int) $order->getId() === $sessionOrderId
                && (int) $order->getQuoteId() === $quoteId
            ) {
                return;
            }
        }

        $this->denyAccess();
    }

    /**
     * @inheritdoc
     */
    public function assertSessionOrder(): void
    {
        try {
            $order = $this->orderManager->getCurrentOrder();
            if ((int) $order->getId() <= 0) {
                $this->denyAccess();
            }
        } catch (\Exception $e) {
            $this->denyAccess();
        }
    }

    /**
     * @inheritdoc
     */
    public function assertPaymentOwnership(string $paymentId): void
    {
        try {
            $paymentOrder = $this->orderManager->getOrderByPaymentId($paymentId);
        } catch (\Exception $e) {
            $this->denyAccess();
            return;
        }

        $customerId = (int) $paymentOrder->getCustomerId();
        $sessionCustomerId = (int) $this->customerSession->getCustomerId();
        if ($customerId > 0 && $sessionCustomerId > 0 && $customerId !== $sessionCustomerId) {
            $this->denyAccess();
            return;
        }

        $paymentOrderId = (int) $paymentOrder->getId();
        $paymentQuoteId = (int) $paymentOrder->getQuoteId();

        $sessionOrder = null;
        try {
            $sessionOrder = $this->orderManager->getCurrentOrder();
        } catch (\Exception $e) {
            $sessionOrder = null;
        }

        if ($sessionOrder !== null && (int) $sessionOrder->getId() === $paymentOrderId) {
            return;
        }

        if ((int) $this->checkoutSession->getLastOrderId() === $paymentOrderId) {
            return;
        }

        $sessionQuoteId = (int) $this->checkoutSession->getQuoteId();
        if ($sessionQuoteId > 0 && $sessionQuoteId === $paymentQuoteId) {
            return;
        }

        $quote = null;
        try {
            $quote = $this->orderManager->getQuote();
        } catch (\Exception $e) {
            $quote = null;
        }

        if ($quote !== null && (int) $quote->getId() === $paymentQuoteId) {
            return;
        }

        $this->denyAccess();
    }

    /**
     * Deny access by throwing a WebAPI exception.
     *
     * @return void
     * @throws WebapiException
     */
    private function denyAccess(): void
    {
        throw new WebapiException(
            __('Access denied. You do not have permission to access this order.'),
            self::ACCESS_DENIED_CODE,
            WebapiException::HTTP_FORBIDDEN
        );
    }
}
