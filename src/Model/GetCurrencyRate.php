<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\QuoteIsNotActiveException;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\GetRateResponse;
use ForumPay\PaymentGateway\Api\GetCurrencyRateInterface;
use ForumPay\PaymentGateway\Model\Data\Rate;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;

/**
 * @inheritdoc
 */
class GetCurrencyRate implements GetCurrencyRateInterface
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
     * @return \ForumPay\PaymentGateway\Api\Data\RateInterface
     * @throws \Exception
     */
    public function getRateForCurrency(string $currency): \ForumPay\PaymentGateway\Api\Data\RateInterface
    {
        try {
            $this->logger->info('GetCurrencyRate entrypoint called.', ['currency' => $currency]);

            /** @var GetRateResponse $response */
            $response = $this->forumPay->getRate($currency);

            $rate = new Rate(
                $response->getInvoiceCurrency(),
                $response->getInvoiceAmount(),
                $response->getCurrency(),
                $response->getRate(),
                $response->getAmountExchange(),
                $response->getNetworkProcessingFee(),
                $response->getAmount(),
                $response->getWaitTime(),
                $response->getSid(),
                $response->getFastTransactionFee(),
                $response->getFastTransactionFeeCurrency(),
                $response->getPaymentId()
            );

            $this->logger->info('GetCurrencyRate entrypoint finished.');

            return $rate;
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
