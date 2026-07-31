<?php

namespace ForumPay\PaymentGateway\Model\Config\Backend;

use ForumPay\PaymentGateway\Helper\Data;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class ValidateApiEnvironment extends Value
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
     * Allow only Production or Sandbox API environment URLs.
     *
     * Require a fresh API secret whenever the environment changes.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $apiEnv = (string) $this->getValue();

        if ($apiEnv === '' || !$this->forumPayConfig->isAllowedApiEnvironment($apiEnv)) {
            throw new LocalizedException(__('Invalid API environment.'));
        }

        $savedApiEnv = (string) $this->getOldValue();

        if (!$this->forumPayConfig->apiEnvChanged($apiEnv, $savedApiEnv)) {
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
