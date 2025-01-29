<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\StartPaymentInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Model\Data\Payer\PayerFactory;
use ForumPay\PaymentGateway\Model\Data\Payment;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Logger\PrivateTokenMasker;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\StartPaymentResponse;
use Magento\Framework\Webapi\Rest\Request;

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
     * @var Request
     */
    private Request $request;

    /**
     * Constructor
     *
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     * @param Request $request
     */
    public function __construct(
        ForumPay $forumPay,
        ForumPayLogger $logger,
        Request $request
    ) {
        $this->forumPay = $forumPay;
        $this->logger = $logger;
        $this->logger->addParser(new PrivateTokenMasker());
        $this->request = $request;
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
     * @throws ApiHttpException
     */
    public function startPayment(
        string $currency,
        ?string $kycPin = null
    ): \ForumPay\PaymentGateway\Api\Data\PaymentInterface {
        try {
            $this->logger->info('StartPayment entrypoint called.', ['currency' => $currency]);

            $request = $this->request->getBodyParams();

            $payer = $request['payer'] ?? null;
            $payer = (new PayerFactory())->create($payer);

            /** @var StartPaymentResponse $response */
            $response = $this->forumPay->startPayment($currency, '', $kycPin, $payer);

            $notices = [];
            foreach ($response->getNotices() as $notice) {
                $notices[] = new Payment\Notice($notice['code'], $notice['message']);
            }

            $beneficiaryVaspDetails = $response->getBeneficiaryVaspDetails();
            $beneficiaryVaspDetails = $beneficiaryVaspDetails ? new Payment\BeneficiaryVaspDetails(
                $beneficiaryVaspDetails['beneficiary_name'] ?? '',
                $beneficiaryVaspDetails['beneficiary_vasp'] ?? '',
                $beneficiaryVaspDetails['beneficiary_vasp_did']
            ) : null;

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
                $beneficiaryVaspDetails,
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
            } elseif ($errorCode === 'missingPayerData' || $errorCode === 'incompletePayerData') {
                throw new ApiHttpException($e, 3056);
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
