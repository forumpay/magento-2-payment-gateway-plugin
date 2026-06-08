<?php

namespace ForumPay\PaymentGateway\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class NetworkProcessingFeePaidBy implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'payer',
                'label' => __('Payer')
            ],
            [
                'value' => 'merchant',
                'label' => __('Merchant')
            ],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'payer'    => __('Payer'),
            'merchant' => __('Merchant'),
        ];
    }
}
