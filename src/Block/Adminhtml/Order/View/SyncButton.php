<?php
namespace ForumPay\PaymentGateway\Block\Adminhtml\Order\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * @inheritdoc
 */
class SyncButton extends AbstractBlock
{
    /**
     * Custom Button for Syncing Status with ForumPay
     *
     * @return string
     *
     * @throws LocalizedException
     */
    protected function _toHtml()
    {
        $orderBlock = $this->getLayout()->getBlock('order_info');
        $order = $orderBlock->getOrder();
        $paymentId = $order->getPayment()->getLastTransId();

        if ($paymentId) {
            return '<p class="order_payment_reference"><span><strong>ForumPay reference:</strong></span> <br/> <span class="order_payment_id">' . $paymentId . '</span></p>
                <div>
                    <button type="button" id="forumpay_api_sync_payment" class="sync-button">Sync status with ForumPay</button>
                </div>';
        }
        return '';
    }
}
