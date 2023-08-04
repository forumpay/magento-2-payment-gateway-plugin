<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Model\Data\Payment;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\Api\StartPaymentInterface;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\StartPaymentResponse;

/**
 * @inheritdoc
 */
class StartPayment implements StartPaymentInterface
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
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentInterface
     * @throws ForumPayException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function startPayment(string $currency): \ForumPay\PaymentGateway\Api\Data\PaymentInterface
    {
        try {
            $this->logger->info('StartPayment entrypoint called.', ['currency' => $currency]);

            /** @var StartPaymentResponse $response */
            $response = $this->forumPay->startPayment($currency, '');

            $notices = [];
            foreach ($response->getNotices() as $notice) {
                $notices[] = new Payment\Notice($notice['code'], $notice['message']);
            }

            $payment = new Payment(
                $response->getPaymentId(),
                $response->getAddress(),
                '',
                $response->getMinConfirmations(),
                $response->getFastTransactionFee(),
                $response->getFastTransactionFeeCurrency(),
                $response->getQr(),
                $response->getQrAlt(),
                $response->getQrImg(),
                $response->getQrAltImg(),
                $notices
            );

            $this->logger->info('StartPayment entrypoint finished.');

            return $payment;
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 3050);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                3100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
