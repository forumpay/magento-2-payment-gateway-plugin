<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\WebhookTestInterface;

class WebhookTest implements WebhookTestInterface
{
    /**
     * Webhook Test Message
     *
     * @var string
     */
    private string $message;

    /**
     * Webhook Test constructor
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
