<?php

namespace ForumPay\PaymentGateway\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class NetworkProcessingFeePaidBy extends Field
{
    /**
     * Render the value cell with the merchant notice appended after the comment
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element)
    {
        $html = parent::_renderValue($element);

        $elementId = $element->getHtmlId() . '_merchant_notice';
        $currentValue = $element->getValue();
        $display = ($currentValue === 'merchant') ? '' : 'display:none;';

        $noticeText = __("This option must be enabled for your account before you can select 'Merchant'. Contact your account representative or");
        $email = 'support@forumpay.com';

        $notice = sprintf(
            '<p id="%s" style="%s">%s <a href="mailto:%s">%s</a></p>',
            $elementId,
            $display,
            $noticeText,
            $email,
            $email
        );

        return str_replace('</td>', $notice . '</td>', $html);
    }
}
