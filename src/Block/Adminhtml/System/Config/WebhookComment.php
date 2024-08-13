<?php

namespace ForumPay\PaymentGateway\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

class WebhookComment extends Field
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get comment text
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $baseUrl = $this->urlBuilder->getBaseUrl();
        $baseUrl = str_replace('localhost', 'my.webshop.com', $baseUrl) . 'rest/V1/forumpay/webhook';

        $comment = "Optional: This URL should point to the endpoint that will handle the webhook events.<br>Typically, it should be: <b><i> {$baseUrl} </i></b><br>This URL will override the default setting for your API keys on your Forumpay account.<br>Ensure that the URL is publicly accessible and can handle the incoming webhook events securely.";

        $element->setComment($comment);
        return parent::_getElementHtml($element);
    }
}
