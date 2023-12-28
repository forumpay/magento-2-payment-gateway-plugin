<?php

namespace ForumPay\PaymentGateway\Api\Data\CurrencyList;

/**
 * Dto of cryptocurrency
 */
interface CurrencyInterface
{
    /**
     * Cryptocurrency symbol (BTC, ETH, etc.)
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Currency description
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * 'OK' if currency can be used
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * If currency supports zero confirmations
     *
     * @return bool
     */
    public function isZeroConfirmationsEnabled(): bool;

    /**
     * FIAT currency
     *
     * @return string
     */
    public function getCurrencyFiat(): string;

    /**
     * Exchange rate
     *
     * @return string|null
     */
    public function getRate(): ?string;

    /**
     * Get currency icon url
     *
     * @return string
     */
    public function getIconUrl(): string;
}
