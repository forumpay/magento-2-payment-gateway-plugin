<?php

namespace ForumPay\PaymentGateway\Model\Config\Source;

use ForumPay\PaymentGateway\Helper\Data;
use Magento\Framework\Option\ArrayInterface;

class ApiEnvironment implements ArrayInterface
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
                'value' => Data::PRODUCTION_URL,
                'label' => __('Production')
            ],
            [
                'value' => Data::SANDBOX_URL,
                'label' => __('Sandbox')
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
            Data::PRODUCTION_URL => __('Production'),
            Data::SANDBOX_URL => __('Sandbox')
        ];
    }
}
