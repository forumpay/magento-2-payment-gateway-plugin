<?php

namespace ForumPay\PaymentGateway\Api\Data;

interface WebhookTestInterface
{
    /**
     * Return message
     *
     * @return string
     */
    public function getMessage(): string;
}
