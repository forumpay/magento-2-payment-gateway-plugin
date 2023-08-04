<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto for specific cryptocurrency exchange rate information
 */
interface RateInterface
{
    /**
     * Currency code for FIAT currency on invoice (EUR, USD, etc.)
     *
     * @return string
     */
    public function getInvoiceCurrency(): string;

    /**
     * Amount on invoice for FIAT currency
     *
     * @return string|null
     */
    public function getInvoiceAmount(): ?string;

    /**
     * Cryptocurrency symbol (BTC, ETH, etc.)
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Exchange rate
     *
     * @return string|null
     */
    public function getRate(): ?string;

    /**
     * Amount exchange
     *
     * @return string|null
     */
    public function getAmountExchange(): ?string;

    /**
     * Amount needed to transfer cryptocurrency from merchant to exchange
     *
     * @return string
     */
    public function getNetworkProcessingFee(): string;

    /**
     * Total amount to pay
     *
     * @return string|null
     */
    public function getAmount(): ?string;

    /**
     * Expected time to confirm
     *
     * @return string
     */
    public function getWaitTime(): string;

    /**
     * Sub Account ID
     *
     * @return string|null
     */
    public function getSid(): ?string;

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
     * Payment ID received from StartPayment
     *
     * @return string
     */
    public function getPaymentId(): string;
}
