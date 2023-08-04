<?php

namespace ForumPay\PaymentGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use ForumPay\PaymentGateway\Helper\Data as ForumPayConfig;
use ForumPay\PaymentGateway\Model\Payment\ForumPay as ForumPayModel;

/**
 * Provide publicly available js variables
 */
class ForumPayConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected string $methodCode = ForumPayModel::PAYMENT_METHOD_CODE;

    /**
     * Payment method instance
     *
     * @var MethodInterface
     */
    protected MethodInterface $method;

    /**
     * ForumPay plugin settings
     *
     * @var ForumPayConfig
     */
    protected ForumPayConfig $forumPayConfig;

    /**
     * ForumPay payment model
     *
     * @var ForumPayModel
     */
    private ForumPayModel $forumPayModel;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Constructor
     *
     * @param PaymentHelper $paymentHelper
     * @param ForumPayConfig $forumPayConfig
     * @param ForumPayModel $forumPayModel
     * @param UrlInterface $urlBuilder
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        ForumPayConfig $forumPayConfig,
        ForumPayModel $forumPayModel,
        UrlInterface $urlBuilder
    ) {
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->forumPayConfig = $forumPayConfig;
        $this->forumPayModel = $forumPayModel;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $image = $this->forumPayConfig->getPaymentMethodIconUrl();

        return $this->method->isAvailable() ? [
            'payment' => [
                $this->methodCode => [
                    'paymentMethodImage' => $image,
                    'isImageVisible' =>  $image !== null,
                    'baseUrl' => $this->urlBuilder->getBaseUrl(),
                    'successResultUrl' => $this->urlBuilder->getUrl('checkout/onepage/success'),
                    'errorResultUrl' => $this->urlBuilder->getUrl('checkout/cart'),
                ],
            ],
        ] : [];
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    protected function getInstructions()
    {
        return $this->forumPayConfig->getInstructions();
    }
}
