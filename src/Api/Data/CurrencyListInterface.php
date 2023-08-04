<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto collection of the available currency list
 */
interface CurrencyListInterface
{
    /**
     * Returns the list of all defined cryptocurrencies from ForumPay
     *
     * @return \ForumPay\PaymentGateway\Api\Data\CurrencyList\CurrencyInterface[]
     */
    public function getCurrencies(): array;
}
