<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for triggering the IPN
 */
interface WebhookInterface
{
    /**
     * Should be triggered by ForumPay any time the payment status changes
     *
     * @return void
     */
    public function execute(): void;
}
