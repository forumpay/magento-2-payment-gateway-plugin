<?php

namespace ForumPay\PaymentGateway\Model\Data\CurrencyList;

use ForumPay\PaymentGateway\Api\Data\CurrencyList\CurrencyInterface;

/**
 * @inheritdoc
 */
class Currency implements CurrencyInterface
{
    /**
     * @var string
     */
    private string $currency;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var bool
     */
    private bool $zeroConfirmationsEnabled;

    /**
     * @var string
     */
    private string $currencyFiat;

    /**
     * @var string
     */
    private string $iconUrl;

    /**
     * @var string|null
     */
    private ?string $rate;

    /**
     * Currency DTO constructor
     *
     * @param string $currency
     * @param string $description
     * @param string $status
     * @param bool $zeroConfirmationsEnabled
     * @param string $currencyFiat
     * @param string $iconUrl
     * @param string|null $rate
     */
    public function __construct(
        string $currency,
        string $description,
        string $status,
        bool $zeroConfirmationsEnabled,
        string $currencyFiat,
        string $iconUrl,
        ?string $rate
    ) {
        $this->currency = $currency;
        $this->description = $description;
        $this->status = $status;
        $this->zeroConfirmationsEnabled = $zeroConfirmationsEnabled;
        $this->currencyFiat = $currencyFiat;
        $this->iconUrl = $iconUrl;
        $this->rate = $rate;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function isZeroConfirmationsEnabled(): bool
    {
        return $this->zeroConfirmationsEnabled;
    }

    /**
     * @inheritdoc
     */
    public function getCurrencyFiat(): string
    {
        return $this->currencyFiat;
    }

    /**
     * @inheritdoc
     */
    public function getIconUrl(): string
    {
        return $this->iconUrl;
    }

    /**
     * @inheritdoc
     */
    public function getRate(): ?string
    {
        return $this->rate;
    }
}
