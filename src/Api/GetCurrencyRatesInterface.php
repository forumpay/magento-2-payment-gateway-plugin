<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for getting rates for multiple currencies from ForumPay
 */
interface GetCurrencyRatesInterface
{
    /**
     * Get rates from ForumPay api for multiple cryptocurrencies
     *
     * @param string $currencies Comma-separated list of cryptocurrency codes (e.g., "BTC,ETH,USDT")
     * @return \ForumPay\PaymentGateway\Api\Data\RatesInterface
     */
    public function getCurrencyRates(string $currencies): \ForumPay\PaymentGateway\Api\Data\RatesInterface;
}
