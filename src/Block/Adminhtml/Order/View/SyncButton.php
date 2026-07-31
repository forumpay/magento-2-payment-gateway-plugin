<?php

namespace ForumPay\PaymentGateway\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;

/**
 * @inheritdoc
 */
class SyncButton extends Template
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
            $syncUrl = $this->getUrl('forumpay/order/syncPayment');
            return '<p class="order_payment_reference"><span><strong>ForumPay reference:</strong></span> <br/> <span class="order_payment_id">' . $paymentId . '</span></p>
                <div>
                    <button type="button" id="forumpay_api_sync_payment" class="sync-button"'
                . ' data-sync-url="' . $this->escapeHtmlAttr($syncUrl) . '">Sync status with ForumPay</button>
                </div>';
        }
        return '';
    }
}
