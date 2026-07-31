<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\PingInterface;
use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Helper\Data as ForumPayConfig;
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
     * @var ForumPayConfig
     */
    private ForumPayConfig $forumPayConfig;

    /**
     * Constructor
     *
     * @param Request $request
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     * @param ForumPayConfig $forumPayConfig
     */
    public function __construct(
        Request $request,
        ForumPay $forumPay,
        ForumPayLogger $logger,
        ForumPayConfig $forumPayConfig
    ) {
        $this->forumPay = $forumPay;
        $this->request = $request;
        $this->logger = $logger;
        $this->forumPayConfig = $forumPayConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute(): \ForumPay\PaymentGateway\Api\Data\PingInterface
    {
        return $this->executeWithParams($this->request->getBodyParams());
    }

    /**
     * Execute ping with explicit request parameters (adminhtml controller).
     *
     * @param array $request
     * @return \ForumPay\PaymentGateway\Api\Data\PingInterface
     * @throws \Exception
     */
    public function executeWithParams(array $request): \ForumPay\PaymentGateway\Api\Data\PingInterface
    {
        try {
            $this->logger->info('Ping entrypoint called.');

            $apiEnv = $request['apiEnv'] ?? '';
            $apiKey = $request['apiKey'] ?? '';
            $apiSecret = $request['apiSecret'] ?? '';
            $apiUrlOverride = trim((string) ($request['apiUrlOverride'] ?? ''));
            $webhookUrl = $request['webhookUrl'] ?? '';

            if ($apiEnv === '') {
                throw new ForumPayException(__('API environment is required.'));
            }

            if (!$this->forumPayConfig->isAllowedApiEnvironment($apiEnv)) {
                throw new ForumPayException(__('Invalid API environment.'));
            }

            $credentials = $this->forumPayConfig->resolveCredentials(
                $apiUrlOverride,
                $apiKey,
                $apiSecret,
                $apiEnv
            );

            $response = $this->forumPay->ping(
                $apiEnv,
                $credentials['apiUser'],
                $credentials['apiSecret'],
                $apiUrlOverride,
                $webhookUrl
            );

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
        } catch (ForumPayException $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw $e;
        } catch (InvalidApiResponseException $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException($e->getMessage(), $e->getCfRayId(), 0);
        } catch (InvalidResponseStatusCodeException $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException(
                $e->getMessage(),
                $e->getCfRayId(),
                $e->getResponseStatusCode()
            );
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ForumPayHttpException(
                $e->getMessage(),
                $e->getCfRayId(),
                $e->getCode() ?? 0
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Exception($e->getMessage(), 500, $e);
        }
    }
}
