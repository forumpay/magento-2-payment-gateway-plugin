<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\StartPaymentInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Model\Data\Payment;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Logger\PrivateTokenMasker;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
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
        $this->logger->addParser(new PrivateTokenMasker());
    }

    /**
     * @inheritdoc
     *
     * @param string $currency
     * @param string|null $kycPin
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentInterface
     * @throws ForumPayException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws ApiExceptionInterface
     */
    public function startPayment(
        string $currency,
        ?string $kycPin = null
    ): \ForumPay\PaymentGateway\Api\Data\PaymentInterface {
        try {
            $this->logger->info('StartPayment entrypoint called.', ['currency' => $currency]);

            /** @var StartPaymentResponse $response */
            $response = $this->forumPay->startPayment($currency, '', $kycPin);

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
                $notices,
                $response->getStatsToken(),
            );

            $this->logger->info('StartPayment entrypoint finished.');

            return $payment;
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            $errorCode = $e->getErrorCode();

            if ($errorCode === null) {
                throw new ApiHttpException($e, 3050);
            }

            if ($errorCode === 'payerAuthNeeded' ||
                $errorCode === 'payerKYCNotVerified' ||
                $errorCode === 'payerKYCNeeded' ||
                $errorCode === 'payerEmailVerificationCodeNeeded'
            ) {
                try {
                    $this->forumPay->requestKyc();
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Webapi\Exception(
                        __($e->getMessage()),
                        3050,
                        \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                    );
                }
                throw new ApiHttpException($e, 3051);
            } elseif (substr($errorCode, 0, 5) === 'payer') {
                throw new ApiHttpException($e, 3052);
            } else {
                throw new ApiHttpException($e, 3050);
            }
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
