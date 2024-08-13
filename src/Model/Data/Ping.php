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
     * Ping DTO constructor
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
