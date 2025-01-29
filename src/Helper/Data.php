<?php

namespace ForumPay\PaymentGateway\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const MODULE_NAME = 'ForumPay_PaymentGateway';

    public const XML_PATH_ENABLED = 'payment/forumpay/active';
    public const XML_PATH_PAYMENT_ENVIRONMENT = 'payment/forumpay/payment_environment';
    public const XML_PATH_PAYMENT_ENVIRONMENT_OVERRIDE = 'payment/forumpay/payment_environment_override';
    public const XML_PATH_MERCHANT_API_USER = 'payment/forumpay/merchant_api_user';
    public const XML_PATH_MERCHANT_PASS = 'payment/forumpay/merchant_api_secret';
    public const XML_PATH_ORDER_STATUS_AFTER_PAYMENT = 'payment/forumpay/order_status_after_payment';
    public const XML_PATH_ORDER_STATUS = 'payment/forumpay/order_status';
    public const XML_PATH_INSTRUCTIONS = 'payment/forumpay/instructions';
    public const XML_PATH_POS_ID = 'payment/forumpay/pos_id';
    public const XML_PATH_WEBHOOK_URL = 'payment/forumpay/webhook_url';
    public const XML_PATH_ACCEPT_ZERO_CONFIRMATIONS = 'payment/forumpay/accept_zero_confirmations';
    public const XML_PATH_PAYMENT_ICON = 'payment/forumpay/payment_icon';
    public const XML_PATH_ACCEPT_UNDERPAYMENT = 'payment/forumpay/accept_underpayment';
    public const XML_PATH_ACCEPT_UNDERPAYMENT_THRESHOLD = 'payment/forumpay/accept_underpayment_threshold';
    public const XML_PATH_ACCEPT_UNDERPAYMENT_MODIFY_ORDER_TOTAL = 'payment/forumpay/accept_underpayment_modify_order_total';
    public const XML_PATH_ACCEPT_UNDERPAYMENT_MODIFY_ORDER_TOTAL_DESCRIPTION = 'payment/forumpay/accept_underpayment_modify_order_total_description';
    public const XML_PATH_ACCEPT_OVERPAYMENT = 'payment/forumpay/accept_overpayment';
    public const XML_PATH_ACCEPT_OVERPAYMENT_THRESHOLD = 'payment/forumpay/accept_overpayment_threshold';
    public const XML_PATH_ACCEPT_OVERPAYMENT_MODIFY_ORDER_TOTAL = 'payment/forumpay/accept_overpayment_modify_order_total';
    public const XML_PATH_ACCEPT_OVERPAYMENT_MODIFY_ORDER_TOTAL_DESCRIPTION = 'payment/forumpay/accept_overpayment_modify_order_total_description';
    public const XML_PATH_ACCEPT_LATE_PAYMENT = 'payment/forumpay/accept_late_payment';

    public const PRODUCTION_URL = 'https://api.forumpay.com/pay/v2/';
    public const SANDBOX_URL = 'https://sandbox.api.forumpay.com/pay/v2/';

    /**
     * The tail part of directory path for uploading
     */
    public const ICON_UPLOAD_DIR = 'forumpay';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var ResolverInterface
     */
    private ResolverInterface $localeResolver;

    /**
     * @var ModuleListInterface
     */
    private ModuleListInterface $moduleList;

    /**
     * Data helper constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     * @param ResolverInterface $localeResolver
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        ResolverInterface $localeResolver,
        ModuleListInterface $moduleList
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->localeResolver = $localeResolver;
        $this->moduleList = $moduleList;
    }

    /**
     * Return get version of this module
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * Check for module is enabled in frontend
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns url to ForumPay api if configured in settings or LIVE by default
     *
     * @return mixed|string
     */
    public function getPaymentMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        ) ?? self::PRODUCTION_URL;
    }

    /**
     * Returns merchant api user
     *
     * @return mixed|string
     */
    public function getMerchantApiUser()
    {
        $apiUser = $this->scopeConfig->getValue(
            self::XML_PATH_MERCHANT_API_USER,
            ScopeInterface::SCOPE_STORE
        );

        return trim($this->encryptor->decrypt($apiUser));
    }

    /**
     * Returns merchant api secret
     *
     * @return string
     */
    public function getMerchantApiSecret()
    {
        $apiSecret = $this->scopeConfig->getValue(
            self::XML_PATH_MERCHANT_PASS,
            ScopeInterface::SCOPE_STORE
        );

        return trim($this->encryptor->decrypt($apiSecret));
    }

    /**
     * Get status that order should be in after the payment
     *
     * @return mixed
     */
    public function getOrderStatusAfterPayment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_STATUS_AFTER_PAYMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get status that order should be in so that payment procedure can start
     *
     * @return mixed
     */
    public function getNewOrderStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Webshop identifier (POS ID). Special characters not allowed. Allowed are: [A-Za-z0-9._-]
     *
     * @return string
     */
    public function getPosId()
    {
        $posId = $this->scopeConfig->getValue(
            self::XML_PATH_POS_ID,
            ScopeInterface::SCOPE_STORE
        );

        if ($posId) {
            return preg_replace(
                '/[^A-Za-z0-9\-]/',
                '',
                str_replace(' ', '-', $posId)
            );
        }

        return 'magento-2';
    }

    /**
     * Returns url to Webhook api depending.
     *
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WEBHOOK_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * If set to true, confirms small payment with zero confirmations
     *
     * @return bool
     */
    public function isAcceptZeroConfirmations(): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_ZERO_CONFIRMATIONS,
            ScopeInterface::SCOPE_STORE
        ) === '1';
    }

    /**
     * Returns custom instructions that should be visible to customer.
     *
     * @return mixed
     */
    public function getInstructions()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTRUCTIONS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns url to FormPay api depending on the environment selected.
     *
     * @return string
     */
    public function getApiUrl()
    {
        $envOverride = $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ENVIRONMENT_OVERRIDE,
            ScopeInterface::SCOPE_STORE
        );

        return  $envOverride ? : $this->getPaymentMode();
    }

    /**
     * Get current store locale string
     *
     * @return string
     */
    public function getStoreLocale()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Returns user specified payment icon
     *
     * @return mixed
     */
    public function getPaymentMethodIcon()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ICON,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns public url of user specified payment icon
     *
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentMethodIconUrl()
    {
        $image = $this->getPaymentMethodIcon();
        if ($image) {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return sprintf('%s%s/%s', $mediaUrl, self::ICON_UPLOAD_DIR, $image);
        }
        return null;
    }

    /**
     * If set to true, confirms to automatically accept payments that are less than the total order amount
     *
     * @return bool
     */
    public function getAcceptUnderpayment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_UNDERPAYMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns maximum percentage of the order total that can be underpaid
     *
     * @return int
     */
    public function getAcceptUnderpaymentThreshold()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_UNDERPAYMENT_THRESHOLD,
            ScopeInterface::SCOPE_STORE
        ) ?: 0;
    }

    /**
     * If set to true, returns modified order total with underpayments as a separate and negative fee
     *
     * @return bool
     */
    public function getAcceptUnderpaymentModifyOrderTotal()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_UNDERPAYMENT_MODIFY_ORDER_TOTAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns a description for the underpayment fee
     *
     * @return string
     */
    public function getAcceptUnderpaymentModifyOrderTotalDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_UNDERPAYMENT_MODIFY_ORDER_TOTAL_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * If set to true, confirms to automatically accept payments that exceed the total order amount
     *
     * @return bool
     */
    public function getAcceptOverpayment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_OVERPAYMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns maximum percentage of the order total that can be overpaid
     *
     * @return int|string
     */
    public function getAcceptOverpaymentThreshold()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_OVERPAYMENT_THRESHOLD,
            ScopeInterface::SCOPE_STORE
        ) ?: '';
    }

    /**
     * If set to true, returns modified order total with overpayments as a separate and positive fee
     *
     * @return bool
     */
    public function getAcceptOverpaymentModifyOrderTotal()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_OVERPAYMENT_MODIFY_ORDER_TOTAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns a description for the overpayment fee
     *
     * @return string
     */
    public function getAcceptOverpaymentModifyOrderTotalDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_OVERPAYMENT_MODIFY_ORDER_TOTAL_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * If set to true, confirms to automatically accept the payment if transaction was received late and either the paid amount is similar to requested or accepting it is allowed by the other Auto-Accept conditions
     *
     * @return bool
     */
    public function getAcceptLatePayment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_LATE_PAYMENT,
            ScopeInterface::SCOPE_STORE
        ) === '1';
    }

    /**
     * Get the installation id
     *
     * @return string|null
     */
    public function getInstallationId()
    {
        return $this->scopeConfig->getValue('forumpay/general/installation_id') ?? '';
    }
}
