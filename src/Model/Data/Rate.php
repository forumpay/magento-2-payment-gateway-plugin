<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\RateInterface;

/**
 * @inheritdoc
 */
class Rate implements RateInterface
{
    /**
     * @var string
     */
    private string $invoiceCurrency;

    /**
     * @var string|null
     */
    private ?string $invoiceAmount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var string|null
     */
    private ?string $rate;

    /**
     * @var string|null
     */
    private ?string $amountExchange;

    /**
     * @var string
     */
    private string $networkProcessingFee;

    /**
     * @var string|null
     */
    private ?string $amount;

    /**
     * @var string
     */
    private string $waitTime;

    /**
     * @var string|null
     */
    private ?string $sid;

    /**
     * @var string|null
     */
    private ?string $fastTransactionFee;

    /**
     * @var string|null
     */
    private ?string $fastTransactionFeeCurrency;

    /**
     * @var string
     */
    private string $paymentId;

    /**
     * Rate DTO constructor
     *
     * @param string $invoiceCurrency
     * @param string|null $invoiceAmount
     * @param string $currency
     * @param string|null $rate
     * @param string|null $amountExchange
     * @param string $networkProcessingFee
     * @param string|null $amount
     * @param string $waitTime
     * @param string|null $sid
     * @param string|null $fastTransactionFee
     * @param string|null $fastTransactionFeeCurrency
     * @param string $paymentId
     */
    public function __construct(
        string $invoiceCurrency,
        ?string $invoiceAmount,
        string $currency,
        ?string $rate,
        ?string $amountExchange,
        string $networkProcessingFee,
        ?string $amount,
        string $waitTime,
        ?string $sid,
        ?string $fastTransactionFee,
        ?string $fastTransactionFeeCurrency,
        string $paymentId
    ) {
        $this->invoiceCurrency = $invoiceCurrency;
        $this->invoiceAmount = $invoiceAmount;
        $this->currency = $currency;
        $this->rate = $rate;
        $this->amountExchange = $amountExchange;
        $this->networkProcessingFee = $networkProcessingFee;
        $this->amount = $amount;
        $this->waitTime = $waitTime;
        $this->sid = $sid;
        $this->fastTransactionFee = $fastTransactionFee;
        $this->fastTransactionFeeCurrency = $fastTransactionFeeCurrency;
        $this->paymentId = $paymentId;
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
    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function getRate(): ?string
    {
        return $this->rate;
    }

    /**
     * @inheritdoc
     */
    public function getAmountExchange(): ?string
    {
        return $this->amountExchange;
    }

    /**
     * @inheritdoc
     */
    public function getNetworkProcessingFee(): string
    {
        return $this->networkProcessingFee;
    }

    /**
     * @inheritdoc
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function getWaitTime(): string
    {
        return $this->waitTime;
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
    public function getFastTransactionFee(): ?string
    {
        return $this->fastTransactionFee;
    }

    /**
     * @inheritdoc
     */
    public function getFastTransactionFeeCurrency(): ?string
    {
        return $this->fastTransactionFeeCurrency;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }
}
