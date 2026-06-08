<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto collection of the available wallet apps
 */
interface WalletAppListInterface
{
    /**
     * Returns the list of all wallet apps from ForumPay
     *
     * @return \ForumPay\PaymentGateway\Api\Data\WalletAppList\WalletAppInterface[]
     */
    public function getWalletApps(): array;
}
