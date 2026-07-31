<?php

namespace ForumPay\PaymentGateway\Plugin\Webapi;

use ForumPay\PaymentGateway\Api\Security\PaymentRouteAccessValidatorInterface;

class PaymentRouteAccessPlugin
{
    /**
     * @var PaymentRouteAccessValidatorInterface
     */
    private PaymentRouteAccessValidatorInterface $accessValidator;

    /**
     * @param PaymentRouteAccessValidatorInterface $accessValidator
     */
    public function __construct(PaymentRouteAccessValidatorInterface $accessValidator)
    {
        $this->accessValidator = $accessValidator;
    }

    /**
     * Restrict getRateForCurrency to active checkout quote.
     *
     * @return void
     */
    public function beforeGetRateForCurrency(): void
    {
        $this->accessValidator->assertActiveCheckoutQuote();
    }

    /**
     * Restrict getCurrencyRates to active checkout quote.
     *
     * @return void
     */
    public function beforeGetCurrencyRates(): void
    {
        $this->accessValidator->assertActiveCheckoutQuote();
    }

    /**
     * Restrict getCurrencyList to active checkout quote.
     *
     * @return void
     */
    public function beforeGetCurrencyList(): void
    {
        $this->accessValidator->assertActiveCheckoutQuote();
    }

    /**
     * Restrict getWalletApps to active checkout quote.
     *
     * @return void
     */
    public function beforeGetWalletApps(): void
    {
        $this->accessValidator->assertActiveCheckoutQuote();
    }

    /**
     * Restrict startPayment to session order.
     *
     * @return void
     */
    public function beforeStartPayment(): void
    {
        $this->accessValidator->assertSessionOrder();
    }

    /**
     * Restrict checkPayment to payment owner.
     *
     * @param mixed $subject
     * @param string $paymentId
     * @return void
     */
    public function beforeCheckPayment($subject, string $paymentId): void
    {
        $this->accessValidator->assertPaymentOwnership($paymentId);
    }

    /**
     * Restrict cancelPayment to payment owner.
     *
     * @param mixed $subject
     * @param string $paymentId
     * @return void
     */
    public function beforeCancelPayment($subject, string $paymentId): void
    {
        $this->accessValidator->assertPaymentOwnership($paymentId);
    }
}
