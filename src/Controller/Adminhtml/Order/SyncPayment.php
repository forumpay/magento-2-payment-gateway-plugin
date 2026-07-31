<?php

namespace ForumPay\PaymentGateway\Controller\Adminhtml\Order;

use ForumPay\PaymentGateway\Model\CheckPayment;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class SyncPayment extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Sales::actions';

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var CheckPayment
     */
    private CheckPayment $checkPayment;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CheckPayment $checkPayment
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CheckPayment $checkPayment
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkPayment = $checkPayment;
    }

    /**
     * Sync payment status and return JSON response.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        $paymentId = $this->getPaymentId();

        if (!$paymentId) {
            return $result->setHttpResponseCode(400)->setData([
                'message' => 'paymentId is required.',
            ]);
        }

        try {
            $paymentDetails = $this->checkPayment->checkPayment($paymentId);

            return $result->setData([
                'order_status_changed' => $paymentDetails->isOrderStatusChanged(),
                'status' => $paymentDetails->getStatus(),
            ]);
        } catch (\Exception $e) {
            return $result->setHttpResponseCode(500)->setData([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment ID from request params or JSON body.
     *
     * @return string|null
     */
    private function getPaymentId(): ?string
    {
        $paymentId = $this->getRequest()->getParam('paymentId');
        if ($paymentId) {
            return (string) $paymentId;
        }

        $content = $this->getRequest()->getContent();
        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || empty($decoded['paymentId'])) {
            return null;
        }

        return (string) $decoded['paymentId'];
    }
}
