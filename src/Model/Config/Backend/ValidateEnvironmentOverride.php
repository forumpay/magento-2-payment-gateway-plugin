<?php

namespace ForumPay\PaymentGateway\Model\Config\Backend;

use ForumPay\PaymentGateway\Helper\Data;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class ValidateEnvironmentOverride extends Value
{
    /**
     * @var Data
     */
    private Data $forumPayConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Data $forumPayConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Data $forumPayConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->forumPayConfig = $forumPayConfig;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Require API credentials when the custom environment URL changes.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $postedOverride = trim((string) $this->getValue());
        $savedOverride = trim((string) $this->getOldValue());

        if (!$this->forumPayConfig->requiresExplicitCredentials($postedOverride, $savedOverride)) {
            return parent::beforeSave();
        }

        if ($this->forumPayConfig->isAdminConfigSaveRequest()) {
            $postedSecret = $this->forumPayConfig->normalizePostedApiSecret(
                $this->forumPayConfig->getPostedForumpayFieldValue('merchant_api_secret')
            );

            if ($this->forumPayConfig->isExplicitCredentialValue($postedSecret)) {
                return parent::beforeSave();
            }

            throw new LocalizedException(
                __('Enter your API Secret to change the API environment.')
            );
        }

        if ($this->forumPayConfig->hasStoredCredentials()) {
            return parent::beforeSave();
        }

        throw new LocalizedException(
            __('Enter your API User and Secret to change the API environment.')
        );
    }
}
