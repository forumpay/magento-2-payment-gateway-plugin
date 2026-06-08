<?php

namespace ForumPay\PaymentGateway\Api\Data\WalletAppList;

/**
 * Dto of a wallet app
 */
interface WalletAppInterface
{
    /**
     * Wallet app ID
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Wallet app name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Wallet app image URL
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * Wallet app image URL for dark mode
     *
     * @return string|null
     */
    public function getImageDarkmode(): ?string;
}
