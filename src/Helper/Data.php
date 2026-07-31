<?php

namespace ForumPay\PaymentGateway\Helper;

use Exception;
use ForumPay\PaymentGateway\Exception\ForumPayException;
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
    public const XML_PATH_NETWORK_PROCESSING_FEE_PAID_BY = 'payment/forumpay/network_processing_fee_paid_by';

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
        $apiEnv = $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        );

        return ($apiEnv !== null && trim((string) $apiEnv) !== '')
            ? (string) $apiEnv
            : self::PRODUCTION_URL;
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
     * Normalize an override URL for comparison: trim, lowercase, strip trailing slashes.
     *
     * @param string $url
     * @return string
     */
    public function normalizeOverrideUrl(string $url): string
    {
        return rtrim(strtolower(trim($url)), '/');
    }

    /**
     * Whether apiEnv is a known ForumPay environment URL (Production or Sandbox).
     *
     * @param string $apiEnv
     * @return bool
     */
    public function isAllowedApiEnvironment(string $apiEnv): bool
    {
        return in_array($apiEnv, [self::PRODUCTION_URL, self::SANDBOX_URL], true);
    }

    /**
     * Saved custom environment URL (same SCOPE_STORE read as other ForumPay getters).
     *
     * @return string
     */
    public function getApiUrlOverride(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ENVIRONMENT_OVERRIDE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Whether both API credentials are stored.
     *
     * @return bool
     */
    public function hasStoredCredentials(): bool
    {
        return $this->hasStoredConfigValue(self::XML_PATH_MERCHANT_API_USER)
            && $this->hasStoredConfigValue(self::XML_PATH_MERCHANT_PASS);
    }

    /**
     * Read a ForumPay config field value from the current admin config save request.
     *
     * @param string $fieldId
     * @return string
     */
    public function getPostedForumpayFieldValue(string $fieldId): string
    {
        $groups = $this->_request->getParam('groups');

        if (!is_array($groups)) {
            return '';
        }

        return trim((string) (
            $groups['forumpay']['fields'][$fieldId]['value'] ?? ''
        ));
    }

    /**
     * Whether the current request is an admin config form save.
     *
     * @return bool
     */
    public function isAdminConfigSaveRequest(): bool
    {
        $groups = $this->_request->getParam('groups');

        return is_array($groups) && isset($groups['forumpay']['fields']);
    }

    /**
     * Whether a posted credential value was explicitly entered in the form.
     *
     * @param string $value
     * @return bool
     */
    public function isExplicitCredentialValue(string $value): bool
    {
        $value = trim($value);

        return $value !== '' && !preg_match('/^\*+$/', $value);
    }

    /**
     * Normalize a posted API secret: mask becomes an empty string.
     *
     * @param string $value
     * @return string
     */
    public function normalizePostedApiSecret(string $value): string
    {
        $value = trim($value);

        if ($value !== '' && preg_match('/^\*+$/', $value)) {
            return '';
        }

        return $value;
    }

    /**
     * Whether a changed custom environment URL requires explicit credentials.
     *
     * @param string $requestedOverride
     * @param string $savedOverride
     * @return bool
     */
    public function requiresExplicitCredentials(string $requestedOverride, string $savedOverride): bool
    {
        $requested = $this->normalizeOverrideUrl($requestedOverride);
        $saved = $this->normalizeOverrideUrl($savedOverride);

        return $requested !== $saved;
    }

    /**
     * Whether the requested API environment differs from the stored one.
     *
     * An empty/missing saved value is treated as Production, matching getPaymentMode().
     * Without this, a never-persisted payment_environment looks like a change when the
     * admin form posts the Production option, and ping/save incorrectly demand a secret.
     *
     * @param string $requestedApiEnv
     * @param string $savedApiEnv
     * @return bool
     */
    public function apiEnvChanged(string $requestedApiEnv, string $savedApiEnv): bool
    {
        $requested = trim($requestedApiEnv);
        $saved = trim($savedApiEnv);

        if ($saved === '') {
            $saved = self::PRODUCTION_URL;
        }

        return $requested !== $saved;
    }

    /**
     * Resolve credentials for ping.
     *
     * API User must always be supplied in the request. An empty API Secret is always
     * rejected. Only Magento's obscure mask (******) may fall back to the stored
     * secret when the environment is unchanged.
     *
     * @param string $requestedOverride
     * @param string $requestedUser
     * @param string $requestedSecret
     * @param string $requestedApiEnv
     * @return array{apiUser: string, apiSecret: string}
     * @throws ForumPayException
     */
    public function resolveCredentials(
        string $requestedOverride,
        string $requestedUser,
        string $requestedSecret,
        string $requestedApiEnv = ''
    ): array {
        $requestedUser = trim($requestedUser);
        $requestedSecret = trim($requestedSecret);

        if ($requestedUser === '') {
            throw new ForumPayException(__('API User is required.'));
        }

        // Empty field is never kept/fallback - only the obscure mask may reuse stored secret.
        if ($requestedSecret === '') {
            throw new ForumPayException(__('API Secret is required.'));
        }

        $requestedSecret = $this->normalizePostedApiSecret($requestedSecret);

        $savedOverride = (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_ENVIRONMENT_OVERRIDE,
            ScopeInterface::SCOPE_STORE
        );

        // Effective env (empty/missing DB value => Production), same as getPaymentMode().
        $savedApiEnv = $this->getPaymentMode();

        $secretExplicit = $this->isExplicitCredentialValue($requestedSecret);

        $overrideChanged = $this->requiresExplicitCredentials($requestedOverride, $savedOverride);
        $apiEnvChanged = $requestedApiEnv !== '' && $this->apiEnvChanged($requestedApiEnv, $savedApiEnv);

        if ($overrideChanged || $apiEnvChanged) {
            if (!$secretExplicit) {
                throw new ForumPayException(
                    __('Enter your API Secret to change the API environment.')
                );
            }

            return ['apiUser' => $requestedUser, 'apiSecret' => $requestedSecret];
        }

        $apiSecret = $secretExplicit ? $requestedSecret : $this->getMerchantApiSecret();

        if ($apiSecret === '') {
            throw new ForumPayException(__('API Secret is required.'));
        }

        return ['apiUser' => $requestedUser, 'apiSecret' => $apiSecret];
    }

    /**
     * Whether a config value exists (SCOPE_STORE, same as other ForumPay getters).
     *
     * @param string $path
     * @return bool
     */
    private function hasStoredConfigValue(string $path): bool
    {
        return !empty($this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE));
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
     * @return int|string
     */
    public function getAcceptUnderpaymentThreshold()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCEPT_UNDERPAYMENT_THRESHOLD,
            ScopeInterface::SCOPE_STORE
        ) ?: '';
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
     * Returns who pays the network processing fee ('payer' or 'merchant')
     *
     * @return string
     */
    public function getNetworkProcessingFeePaidBy(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NETWORK_PROCESSING_FEE_PAID_BY,
            ScopeInterface::SCOPE_STORE
        ) ?? 'payer';
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
