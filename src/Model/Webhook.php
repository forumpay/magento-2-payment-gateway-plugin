<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\WebhookInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Response\CheckPaymentResponse;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;

/**
 * @inheritdoc
 */
class Webhook implements WebhookInterface
{
    /**
     * ForumPay payment model
     *
     * @var ForumPay
     */
    private ForumPay $forumPay;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var ForumPayLogger
     */
    private ForumPayLogger $logger;
    /**
     * Constructor
     *
     * @param Request $request
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     */
    public function __construct(
        Request $request,
        ForumPay $forumPay,
        ForumPayLogger $logger
    ) {
        $this->forumPay = $forumPay;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     * @throws \ForumPay\PaymentGateway\Exception\ForumPayException
     */
    public function execute(): void
    {
        try {
            $request = $this->request->getBodyParams();
            $this->logger->info('Webhook entrypoint called.', ['request' => $request]);

            $paymentId = $request['payment_id'] ?? null;

            if (!$paymentId) {
                throw new ForumPayException(__('PaymentId not found in request'));
            }

            /** @var CheckPaymentResponse $response */
            $this->forumPay->checkPayment($paymentId);

            $this->logger->info('Webhook entrypoint finished.');
        } catch (ForumPayException $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                6001,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 6050);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                6100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
