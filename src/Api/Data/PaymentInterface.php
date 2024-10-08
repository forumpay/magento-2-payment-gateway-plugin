<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto for basic payment information
 */
interface PaymentInterface
{
    /**
     * Get unique payment id from ForumPay
     *
     * @return string
     */
    public function getPaymentId(): string;

    /**
     * Get payment address connected to the payment id
     *
     * @return string
     */
    public function getAddress(): string;

    /**
     * Amount missing from initial payment to complete payment.
     *
     * @return string
     */
    public function getMissingAmount(): string;

    /**
     * Minimum confirmation pears needed
     *
     * @return int
     */
    public function getMinConfirmations(): int;

    /**
     * Minimum TX fee/Gas price for fast transaction.
     *
     * @return string|null
     */
    public function getFastTransactionFee(): ?string;

    /**
     * Currency for fast_transaction_fee (e.g.: BTC/byte, Gwei/Gas)
     *
     * @return string|null
     */
    public function getFastTransactionFeeCurrency(): ?string;

    /**
     * String for qr code that includes cryptocurrency, address and amount
     *
     * @return string
     */
    public function getQr(): string;

    /**
     * Alternative string for qr code for legacy wallets including address only
     *
     * @return string
     */
    public function getQrAlt(): string;

    /**
     * URL for qr code image download
     *
     * @return string
     */
    public function getQrImg(): string;

    /**
     * URL for qr_alt code image download
     *
     * @return string
     */
    public function getQrAltImg(): string;

    /**
     * Returns the list of all notices
     *
     * @return \ForumPay\PaymentGateway\Api\Data\Payment\NoticeInterface[]
     */
    public function getNotices(): array;

    /**
     * Returns StatsToken
     *
     * @return string
     */
    public function getStatsToken(): string;
}
