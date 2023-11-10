<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for starting the payment and getting the address and rate information
 */
interface StartPaymentInterface
{
    /**
     * Create payment on forum pay, convert the quote to order and set status
     *
     * @param string $currency
     * @param string|null $kycPin
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentInterface
     */
    public function startPayment(string $currency, ?string $kycPin = null): \ForumPay\PaymentGateway\Api\Data\PaymentInterface;
}
