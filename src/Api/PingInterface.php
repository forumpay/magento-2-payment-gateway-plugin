<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for testing API credentials
 */
interface PingInterface
{
    /**
     * Ping api to check configuration
     *
     * @return \ForumPay\PaymentGateway\Api\Data\PingInterface
     */
    public function execute(): \ForumPay\PaymentGateway\Api\Data\PingInterface;
}
