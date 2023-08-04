<?php

namespace ForumPay\PaymentGateway\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order Status source model
 */
class ProcessingPayment extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [
        Order::STATE_NEW,
        Order::STATE_PENDING_PAYMENT,
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
        Order::STATE_CLOSED,
        Order::STATE_CANCELED,
        Order::STATE_HOLDED,
    ];
}
