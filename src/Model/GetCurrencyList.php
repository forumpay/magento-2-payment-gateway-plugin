<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\GetCurrencyListInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\Model\Data\CurrencyList;
use ForumPay\PaymentGateway\Model\Data\CurrencyList\Currency;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;

/**
 * @inheritdoc
 */
class GetCurrencyList implements GetCurrencyListInterface
{
    /**
     * ForumPay payment model
     *
     * @var ForumPay
     */
    private ForumPay $forumPay;

    /**
     * @var ForumPayLogger
     */
    private ForumPayLogger $logger;
    /**
     * Constructor
     *
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     */
    public function __construct(
        ForumPay $forumPay,
        ForumPayLogger $logger
    ) {
        $this->forumPay = $forumPay;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     *
     * @param string $currency
     * @return \ForumPay\PaymentGateway\Api\Data\CurrencyListInterface
     * @throws \Exception
     */
    public function getCurrencyList(string $currency = null): \ForumPay\PaymentGateway\Api\Data\CurrencyListInterface
    {
        try {
            $this->logger->info('GetCurrencyList entrypoint called.', ['currency' => $currency]);
            $response = $this->forumPay->getCryptoCurrencyList($currency);

            /** @var CurrencyList[] $currencyDtos */
            $currencyDtos = [];

            /** @var \ForumPay\PaymentGateway\PHPClient\Response\GetCurrencyList\Currency $currency */
            foreach ($response->getCurrencies() as $currency) {
                if ($currency->getStatus() !== 'OK') {
                    continue;
                }

                $currencyDto = new Currency(
                    $currency->getCurrency(),
                    $currency->getDescription(),
                    $currency->getSellStatus(),
                    (bool)$currency->getZeroConfirmationsEnabled(),
                    $currency->getCurrencyFiat(),
                    $currency->getRate()
                );
                $currencyDtos[] = $currencyDto;
            }

            $this->logger->debug('GetCurrencyList response.', ['response' => $currencyDtos]);
            $this->logger->info('GetCurrencyList entrypoint finished.');

            return new CurrencyList($currencyDtos);
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 1050);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                1100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
