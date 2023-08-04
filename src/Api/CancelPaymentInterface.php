<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for canceling existing payment
 */
interface CancelPaymentInterface
{
    /**
     * Cancel existing payment on ForumPay
     *
     * @param string $paymentId
     * @param string $reason
     * @param string $description
     * @return void
     */
    public function cancelPayment(string $paymentId, string $reason = '', string $description = ''): void;
}
