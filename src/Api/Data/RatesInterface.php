<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto for rates information for multiple cryptocurrencies
 */
interface RatesInterface
{
    /**
     * Payment ID received from StartPayment
     *
     * @return string
     */
    public function getPaymentId(): string;

    /**
     * Amount on invoice for FIAT currency
     *
     * @return string
     */
    public function getInvoiceAmount(): string;

    /**
     * Currency code for FIAT currency on invoice (EUR, USD, etc.)
     *
     * @return string
     */
    public function getInvoiceCurrency(): string;

    /**
     * Sub Account ID
     *
     * @return string|null
     */
    public function getSid(): ?string;

    /**
     * Currency rates as JSON-encoded string (associative array with currency codes as keys)
     *
     * @return string
     */
    public function getCurrencies(): string;
}
