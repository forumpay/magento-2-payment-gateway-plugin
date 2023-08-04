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
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentInterface
     */
    public function startPayment(string $currency): \ForumPay\PaymentGateway\Api\Data\PaymentInterface;
}
