<?php

namespace ForumPay\PaymentGateway\Api\Data;

interface PingInterface
{
    /**
     * Return message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Return webhook success
     *
     * @return string|null
     */
    public function getWebhookSuccess(): ?string;

    /**
     * Return webhook ping response
     *
     * @return \ForumPay\PaymentGateway\Api\Data\WebhookPingResponseInterface|null
     */
    public function getWebhookPingResponse(): ?\ForumPay\PaymentGateway\Api\Data\WebhookPingResponseInterface;
}
