<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\RatesInterface;

/**
 * @inheritdoc
 */
class Rates implements RatesInterface
{
    /**
     * @var string
     */
    private string $paymentId;

    /**
     * @var string
     */
    private string $invoiceAmount;

    /**
     * @var string
     */
    private string $invoiceCurrency;

    /**
     * @var string|null
     */
    private ?string $sid;

    /**
     * @var string
     */
    private string $currencies;

    /**
     * Rates DTO constructor
     *
     * @param string $paymentId
     * @param string $invoiceAmount
     * @param string $invoiceCurrency
     * @param string|null $sid
     * @param string $currencies JSON-encoded currency rates
     */
    public function __construct(
        string $paymentId,
        string $invoiceAmount,
        string $invoiceCurrency,
        ?string $sid,
        string $currencies
    ) {
        $this->paymentId = $paymentId;
        $this->invoiceAmount = $invoiceAmount;
        $this->invoiceCurrency = $invoiceCurrency;
        $this->sid = $sid;
        $this->currencies = $currencies;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceAmount(): string
    {
        return $this->invoiceAmount;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceCurrency(): string
    {
        return $this->invoiceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function getSid(): ?string
    {
        return $this->sid;
    }

    /**
     * @inheritdoc
     */
    public function getCurrencies(): string
    {
        return $this->currencies;
    }
}
