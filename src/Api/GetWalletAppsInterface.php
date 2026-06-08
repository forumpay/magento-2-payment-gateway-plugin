<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for getting collection of available wallet apps from ForumPay
 */
interface GetWalletAppsInterface
{
    /**
     * Get collection of all wallet apps available to consumer
     *
     * @return \ForumPay\PaymentGateway\Api\Data\WalletAppListInterface
     */
    public function getWalletApps(): \ForumPay\PaymentGateway\Api\Data\WalletAppListInterface;
}
