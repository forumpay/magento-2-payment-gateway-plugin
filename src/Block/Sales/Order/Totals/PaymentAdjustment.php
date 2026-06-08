<?php

declare(strict_types=1);

namespace ForumPay\PaymentGateway\Block\Sales\Order\Totals;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

/**
 * Display ForumPay payment adjustment in order totals
 */
class PaymentAdjustment extends Template
{
    /**
     * Initialize order totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        if (!$parent) {
            return $this;
        }

        $source = $parent->getSource();
        if (!$source) {
            return $this;
        }

        // For invoices/creditmemos, we need to get the order
        $order = method_exists($source, 'getOrder') ? $source->getOrder() : $source;

        $adjustmentAmount = $order->getData('forumpay_payment_adjustment');
        $baseAdjustmentAmount = $order->getData('base_forumpay_payment_adjustment');
        $adjustmentDescription = $order->getData('forumpay_payment_adjustment_description');

        if ($adjustmentAmount !== null && floatval($adjustmentAmount) != 0) {
            $total = new DataObject([
                'code' => 'forumpay_payment_adjustment',
                'strong' => false,
                'value' => $adjustmentAmount,
                'base_value' => $baseAdjustmentAmount,
                'label' => $adjustmentDescription ?: __('Payment Adjustment'),
            ]);

            $parent->addTotal($total, 'grand_total');
        }

        return $this;
    }
}
