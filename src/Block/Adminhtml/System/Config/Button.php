<?php
namespace ForumPay\PaymentGateway\Block\Adminhtml\System\Config;

use ForumPay\PaymentGateway\Helper\Data;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    /**
     * @var Data
     */
    private Data $forumPayConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $forumPayConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $forumPayConfig,
        array $data = []
    ) {
        $this->forumPayConfig = $forumPayConfig;
        parent::__construct($context, $data);
    }

    /**
     * Custom Button for Ping
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $pingUrl = $this->getUrl('forumpay/gateway/ping');
        $html = '<button id="payment_forumpay_api_test" type="button" class="scalable"'
            . ' data-ping-url="' . $this->escapeHtmlAttr($pingUrl) . '"'
            . ' data-saved-override="'
            . $this->escapeHtmlAttr($this->forumPayConfig->getApiUrlOverride()) . '"'
            . ' style="background-color: #eb5202; color: #fff; border: 0">';
        $html .= __('Test API credentials');
        $html .= '</button>';

        return $html;
    }
}
