<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\PingInterface;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Exception\ForumPayHttpException;
use ForumPay\PaymentGateway\Model\Data\WebhookPingResponse;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\InvalidApiResponseException;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\InvalidResponseStatusCodeException;
use Magento\Framework\Webapi\Rest\Request;

class Ping implements PingInterface
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
     */
    public function execute(): \ForumPay\PaymentGateway\Api\Data\PingInterface
    {
        try {
            $this->logger->info('Ping entrypoint called.');

            try {
                $request = $this->request->getBodyParams();

                $apiEnv = $request['apiEnv'];
                $apiKey = $request['apiKey'];
                $apiSecret = $request['apiSecret'];
                $apiUrlOverride = $request['apiUrlOverride'];
                $webhookUrl = $request['webhookUrl'];
            } catch (\InvalidArgumentException $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                throw new ForumPayException(__('There has been an error, check logs for more.'));
            }

            $response = $this->forumPay->ping($apiEnv, $apiKey, $apiSecret, $apiUrlOverride, $webhookUrl);

            $this->logger->debug('Ping response.', ['response' => $response->toArray()]);
            $this->logger->info('Ping entrypoint finished.');

            $webhookPingResult = $response->getWebhookResult();

            if ($webhookPingResult) {
                $webhookPing = new WebhookPingResponse(
                    $webhookPingResult['status'],
                    $webhookPingResult['duration'],
                    $webhookPingResult['webhook_url'],
                    $webhookPingResult['response_code'],
                    json_decode($webhookPingResult['response_body'])->message ?? $webhookPingResult['response_body'],
                );

                $webhookSuccess = $webhookPingResult['status'] === 'ok'
                    && hash('sha256', $this->forumPay->getInstanceIdentifier()) === $webhookPing->getResponseBody();

                return new \ForumPay\PaymentGateway\Model\Data\Ping(
                    'OK',
                    $webhookSuccess ? 'OK' : 'FAILED',
                    $webhookPing
                );
            }

            return new \ForumPay\PaymentGateway\Model\Data\Ping('OK');
        } catch (InvalidApiResponseException $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException($e->getMessage(), $e->getCfRayId(), (int)0);
        } catch (InvalidResponseStatusCodeException $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException($e->getMessage(), $e->getCfRayId(), $e->getResponseStatusCode() ?? 500);
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException($e->getMessage(), $e->getCfRayId(), $e->getCode() ?? 500);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Exception($e->getMessage(), 500, $e);
        }
    }
}
