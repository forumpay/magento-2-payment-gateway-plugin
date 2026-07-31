<?php

namespace ForumPay\PaymentGateway\Controller\Adminhtml\Gateway;

use ForumPay\PaymentGateway\Exception\ForumPayException;
use ForumPay\PaymentGateway\Exception\ForumPayHttpException;
use ForumPay\PaymentGateway\Model\Ping as PingModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Ping extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Config::config';

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var PingModel
     */
    private PingModel $ping;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PingModel $ping
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PingModel $ping
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ping = $ping;
    }

    /**
     * Execute ping request and return JSON response.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();

        try {
            $params = $this->getRequestParams();
            $pingResponse = $this->ping->executeWithParams($params);

            $webhookPingResponse = $pingResponse->getWebhookPingResponse();
            $payload = [
                'message' => $pingResponse->getMessage(),
                'webhook_success' => $pingResponse->getWebhookSuccess(),
                'webhook_ping_response' => $webhookPingResponse ? [
                    'status' => $webhookPingResponse->getStatus(),
                    'duration' => $webhookPingResponse->getDuration(),
                    'webhook_url' => $webhookPingResponse->getWebhookUrl(),
                    'response_code' => $webhookPingResponse->getResponseCode(),
                    'response_body' => $webhookPingResponse->getResponseBody(),
                ] : null,
            ];

            return $result->setData($payload);
        } catch (ForumPayException $e) {
            return $result->setHttpResponseCode(400)->setData([
                'message' => $e->getMessage(),
            ]);
        } catch (ForumPayHttpException $e) {
            return $result->setHttpResponseCode($e->getHttpCode())->setData([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'cfray_id' => $e->getCfRayId(),
            ]);
        } catch (\Exception $e) {
            return $result->setHttpResponseCode(500)->setData([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get request parameters from JSON body or query params.
     *
     * @return array
     */
    private function getRequestParams(): array
    {
        $content = $this->getRequest()->getContent();
        if ($content) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $this->getRequest()->getParams();
    }
}
