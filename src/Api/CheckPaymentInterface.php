<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for checking existing payment
 */
interface CheckPaymentInterface
{
    /**
     * Get payment information from ForumPay api
     *
     * @param string $paymentId
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentDetailsInterface
     */
    public function checkPayment(string $paymentId): \ForumPay\PaymentGateway\Api\Data\PaymentDetailsInterface;
}
