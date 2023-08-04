<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\CurrencyListInterface;
use ForumPay\PaymentGateway\Model\Data\CurrencyList\Currency;

/**
 * @inheritdoc
 */
class CurrencyList implements CurrencyListInterface
{
    /**
     * @var Currency[]
     */
    private array $currencies;

    /**
     * CurrencyList DTO constructor
     *
     * @param Currency[] $currencies
     */
    public function __construct(
        array $currencies
    ) {
        $this->currencies = $currencies;
    }

    /**
     * @inheritdoc
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }
}
