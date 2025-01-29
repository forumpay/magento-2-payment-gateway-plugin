<?php

namespace ForumPay\PaymentGateway\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class GenerateInstallationId implements DataPatchInterface
{
    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Generate installation id constructor
     *
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function apply(): void
    {
        if ($this->scopeConfig->getValue('forumpay/general/installation_id')) {
            return;
        }

        $installationId = bin2hex(random_bytes(16));

        $this->configWriter->save(
            'forumpay/general/installation_id',
            $installationId
        );
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
