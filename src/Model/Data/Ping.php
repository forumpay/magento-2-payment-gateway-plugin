<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\PingInterface;

class Ping implements PingInterface
{
    /**
     * Response message
     *
     * @var string
     */
    private string $message;

    /**
     * Response webhook Success
     *
     * @var string|null
     */
    private ?string $webhookSuccess;

    /**
     * Response Webhook Ping Response
     *
     * @var WebhookPingResponse|null
     */
    private ?WebhookPingResponse $webhookPingResponse;

    /**
     * Ping DTO constructor
     *
     * @param string $message
     * @param string|null $webhookSuccess
     * @param WebhookPingResponse|null $webhookPingResponse
     */
    public function __construct(
        string $message,
        ?string $webhookSuccess = null,
        ?WebhookPingResponse $webhookPingResponse = null
    ) {
        $this->message = $message;
        $this->webhookSuccess = $webhookSuccess;
        $this->webhookPingResponse = $webhookPingResponse;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function getWebhookSuccess(): ?string
    {
        return $this->webhookSuccess;
    }

    /**
     * @inheritdoc
     */
    public function getWebhookPingResponse(): ?WebhookPingResponse
    {
        return $this->webhookPingResponse;
    }
}
