<?php
namespace ForumPay\PaymentGateway\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    /**
     * Custom Button for Ping
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<button id="payment_forumpay_api_test" type="button" class="scalable" style="background-color: #eb5202; color: #fff; border: 0">';
        $html .= __('Test API credentials');
        $html .= '</button>';

        return $html;
    }
}
