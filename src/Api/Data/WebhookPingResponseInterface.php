<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto for webhook ping response
 */
interface WebhookPingResponseInterface
{
    /**
     * Status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Duration
     *
     * @return float
     */
    public function getDuration(): float;

    /**
     * Webhook Url
     *
     * @return string
     */
    public function getWebhookUrl(): string;

    /**
     * Response Code
     *
     * @return int|null
     */
    public function getResponseCode(): ?int;

    /**
     * Response Body
     *
     * @return string|null
     */
    public function getResponseBody(): ?string;
}
