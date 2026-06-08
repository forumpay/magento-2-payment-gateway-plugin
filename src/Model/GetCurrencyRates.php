<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\GetCurrencyRatesInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\QuoteIsNotActiveException;
use ForumPay\PaymentGateway\Model\Data\Rates;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Logger\PrivateTokenMasker;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\GetRatesResponse;

/**
 * @inheritdoc
 */
class GetCurrencyRates implements GetCurrencyRatesInterface
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
        $this->logger->addParser(new PrivateTokenMasker());
    }

    /**
     * @inheritdoc
     *
     * @param string $currencies Comma-separated list of cryptocurrency codes (e.g., "BTC,ETH,USDT")
     * @return \ForumPay\PaymentGateway\Api\Data\RatesInterface
     * @throws \Exception
     */
    public function getCurrencyRates(string $currencies): \ForumPay\PaymentGateway\Api\Data\RatesInterface
    {
        try {
            $this->logger->info('GetCurrencyRates entrypoint called.', ['currencies' => $currencies]);

            /** @var GetRatesResponse $response */
            $response = $this->forumPay->getRates($currencies);

            // JSON encode currencies for Magento Web API compatibility
            $rates = new Rates(
                $response->getPaymentId(),
                $response->getInvoiceAmount(),
                $response->getInvoiceCurrency(),
                $response->getSid(),
                json_encode($response->getCurrencies())
            );

            $this->logger->info('GetCurrencyRates entrypoint finished.');

            return $rates;
        } catch (QuoteIsNotActiveException $e) {
            $this->logger->info($e->getMessage(), $e->getTrace());

            throw new \Magento\Framework\Webapi\Exception(
                __('Specified request cannot be processed.'),
                2001,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 2050);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                2100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
