<?php

namespace ForumPay\PaymentGateway\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Render API User as plaintext. Core Encrypted + type="text" leaves ciphertext in the input.
 */
class MerchantApiUser extends Field
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param EncryptorInterface $encryptor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        EncryptorInterface $encryptor,
        array $data = []
    ) {
        $this->encryptor = $encryptor;
        parent::__construct($context, $data);
    }

    /**
     * Decrypt the stored API user before rendering the text input.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $value = trim((string) $element->getValue());

        // Magento encrypted config values look like "0:3:<base64>".
        if ($value !== '' && preg_match('/^\d+:\d+:/', $value)) {
            $element->setValue(trim($this->encryptor->decrypt($value)));
        }

        return parent::_getElementHtml($element);
    }
}
