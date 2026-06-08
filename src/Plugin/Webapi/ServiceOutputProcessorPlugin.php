<?php

namespace ForumPay\PaymentGateway\Plugin\Webapi;

use ForumPay\PaymentGateway\Api\Data\RatesInterface;
use ForumPay\PaymentGateway\Api\Data\WalletAppListInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;

/**
 * Plugin to handle custom serialization for RatesInterface and WalletAppListInterface
 */
class ServiceOutputProcessorPlugin
{
    /**
     * After plugin for convertValue to handle custom serialization
     *
     * @param ServiceOutputProcessor $subject
     * @param mixed $result
     * @param mixed $data
     * @param string $type
     * @return mixed
     */
    public function afterConvertValue(
        ServiceOutputProcessor $subject,
        $result,
        $data,
        string $type
    ) {
        // Check if the result is for our RatesInterface
        if ($data instanceof RatesInterface && is_array($result)) {
            // Decode the currencies JSON string to an object for proper JSON serialization
            if (isset($result['currencies']) && is_string($result['currencies'])) {
                $result['currencies'] = json_decode($result['currencies'], false);
            }
        }

        // Check if the result is for our WalletAppListInterface
        // Convert snake_case 'wallet_apps' to camelCase 'walletApps' for widget compatibility
        if ($data instanceof WalletAppListInterface && is_array($result)) {
            if (isset($result['wallet_apps'])) {
                $result['walletApps'] = $this->convertWalletAppsToCamelCase($result['wallet_apps']);
                unset($result['wallet_apps']);
            }
        }

        return $result;
    }

    /**
     * Convert wallet apps array keys from snake_case to camelCase
     *
     * @param array $walletApps
     * @return array
     */
    private function convertWalletAppsToCamelCase(array $walletApps): array
    {
        return array_map(function ($walletApp) {
            $converted = [];
            foreach ($walletApp as $key => $value) {
                $camelKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
                $converted[$camelKey] = $value;
            }
            return $converted;
        }, $walletApps);
    }
}
