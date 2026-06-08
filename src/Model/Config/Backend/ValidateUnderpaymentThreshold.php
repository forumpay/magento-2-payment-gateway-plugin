<?php

namespace ForumPay\PaymentGateway\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class ValidateUnderpaymentThreshold extends Value
{
    /**
     * Validate underpayment threshold before save
     *
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = str_replace(',', '.', $this->getValue());

        if ($value === '') {
            $this->setValue('');
            return parent::beforeSave();
        }

        if (!is_numeric($value) || $value < 0.01 || $value > 99.99) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please enter a value between 0 and 100 or leave blank to accept any underpayment amount.')
            );
        }

        $this->setValue((float) $value);
        return parent::beforeSave();
    }
}
