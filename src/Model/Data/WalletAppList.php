<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\WalletAppListInterface;
use ForumPay\PaymentGateway\Model\Data\WalletAppList\WalletApp;

/**
 * @inheritdoc
 */
class WalletAppList implements WalletAppListInterface
{
    /**
     * @var WalletApp[]
     */
    private array $walletApps;

    /**
     * WalletAppList DTO constructor
     *
     * @param WalletApp[] $walletApps
     */
    public function __construct(
        array $walletApps
    ) {
        $this->walletApps = $walletApps;
    }

    /**
     * @inheritdoc
     */
    public function getWalletApps(): array
    {
        return $this->walletApps;
    }
}
