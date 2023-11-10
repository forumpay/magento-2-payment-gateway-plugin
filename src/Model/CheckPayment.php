<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\CheckPaymentInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\TransactionDetailsMissingException;
use ForumPay\PaymentGateway\Model\Data\PaymentDetails;
use ForumPay\PaymentGateway\Model\Data\PaymentDetails\Underpayment;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Logger\PrivateTokenMasker;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\CheckPaymentResponse;

/**
 * @inheritdoc
 */
class CheckPayment implements CheckPaymentInterface
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
     * @param string $paymentId
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentDetailsInterface
     * @throws \Exception
     */
    public function checkPayment(string $paymentId): \ForumPay\PaymentGateway\Api\Data\PaymentDetailsInterface
    {
        try {
            $this->logger->info('CheckPayment entrypoint called.', ['paymentId' => $paymentId]);

            /** @var CheckPaymentResponse $response */
            $response = $this->forumPay->checkPayment($paymentId);

            if ($response->getUnderpayment()) {
                $underPayment = new Underpayment(
                    $response->getUnderpayment()->getAddress(),
                    $response->getUnderpayment()->getMissingAmount(),
                    $response->getUnderpayment()->getQr(),
                    $response->getUnderpayment()->getQrAlt(),
                    $response->getUnderpayment()->getQrImg(),
                    $response->getUnderpayment()->getQrAltImg()
                );
                $this->logger->debug('CheckPayment - Underpayment.', ['paymentId' => $paymentId]);
            }

            $paymentDetails = new PaymentDetails(
                $response->getReferenceNo(),
                $response->getInserted(),
                $response->getInvoiceAmount(),
                $response->getType(),
                $response->getInvoiceCurrency(),
                $response->getAmount(),
                $response->getMinConfirmations(),
                $response->isAcceptZeroConfirmations(),
                $response->isRequireKytForConfirmation(),
                $response->getCurrency(),
                $response->isConfirmed(),
                $response->getConfirmedTime(),
                $response->getReason(),
                $response->getPayment(),
                $response->getSid(),
                $response->getConfirmations(),
                $response->getAccessToken(),
                $response->getAccessUrl(),
                $response->getWaitTime(),
                $response->getStatus(),
                $response->isCancelled(),
                $response->getCancelledTime(),
                $response->getPrintString(),
                $response->getState(),
                $underPayment ?? null,
            );

            $this->logger->info('CheckPayment entrypoint finished.');

            return $paymentDetails;
        } catch (TransactionDetailsMissingException $e) {
            $this->logger->info($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __('Specified request cannot be processed.'),
                4001,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 4050);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                4100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
