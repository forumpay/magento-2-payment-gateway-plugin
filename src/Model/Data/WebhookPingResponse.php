<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\WebhookPingResponseInterface;

/**
 * @inheritdoc
 */
class WebhookPingResponse implements WebhookPingResponseInterface
{
    /**
     * @var string
     */
    private string $status;

    /**
     * @var float
     */
    private float $duration;

    /**
     * @var string
     */
    private string $webhookUrl;

    /**
     * @var int|null
     */
    private ?int $responseCode;

    /**
     * @var string|null
     */
    private ?string $responseBody;

    /**
     * WebhookPing DTO constructor
     *
     * @param string $status
     * @param float $duration
     * @param string $webhookUrl
     * @param int|null $responseCode
     * @param string|null $responseBody
     */
    public function __construct(
        string $status,
        float $duration,
        string $webhookUrl,
        ?int $responseCode,
        ?string $responseBody
    ) {
        $this->status = $status;
        $this->duration = $duration;
        $this->webhookUrl = $webhookUrl;
        $this->responseCode = $responseCode;
        $this->responseBody = $responseBody;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @inheritdoc
     */
    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    /**
     * @inheritdoc
     */
    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    /**
     * @inheritdoc
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
