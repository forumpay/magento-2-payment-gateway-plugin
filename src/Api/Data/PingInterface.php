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
}
