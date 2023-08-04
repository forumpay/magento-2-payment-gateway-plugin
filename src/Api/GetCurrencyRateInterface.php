<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for getting free currency rate information form ForumPay
 */
interface GetCurrencyRateInterface
{
    /**
     * Get rate from ForumPay api for a selected cryptocurrency
     *
     * @param string $currency
     * @return \ForumPay\PaymentGateway\Api\Data\RateInterface
     */
    public function getRateForCurrency(string $currency): \ForumPay\PaymentGateway\Api\Data\RateInterface;
}
