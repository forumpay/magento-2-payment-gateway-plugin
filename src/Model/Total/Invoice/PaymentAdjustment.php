<?php

declare(strict_types=1);

namespace ForumPay\PaymentGateway\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Invoice total collector for ForumPay payment adjustment (underpayment/overpayment)
 */
class PaymentAdjustment extends AbstractTotal
{
    /**
     * Adjust invoice grand total to include the ForumPay payment adjustment
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice): self
    {
        $order = $invoice->getOrder();
        $adjustment = $order->getData('forumpay_payment_adjustment');
        $baseAdjustment = $order->getData('base_forumpay_payment_adjustment');

        if ($adjustment === null || floatval($adjustment) == 0) {
            return $this;
        }

        $invoice->setGrandTotal($invoice->getGrandTotal() + $adjustment);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseAdjustment);

        return $this;
    }
}
