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
     * @return \ForumPay\PaymentGateway\Api\Data\WebhookTestInterface|null
     */
    public function execute(): ?\ForumPay\PaymentGateway\Api\Data\WebhookTestInterface;
}
