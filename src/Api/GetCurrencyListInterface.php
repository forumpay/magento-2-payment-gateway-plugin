<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for getting collection of available currencies form ForumPay
 */
interface GetCurrencyListInterface
{
    /**
     * Get collection of all cryptocurrencies available to consumer
     *
     * @param string $currency
     * @return \ForumPay\PaymentGateway\Api\Data\CurrencyListInterface
     */
    public function getCurrencyList(string $currency = null): \ForumPay\PaymentGateway\Api\Data\CurrencyListInterface;
}
