<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\GetWalletAppsInterface;
use ForumPay\PaymentGateway\Exception\ApiHttpException;
use ForumPay\PaymentGateway\Model\Data\WalletAppList;
use ForumPay\PaymentGateway\Model\Data\WalletAppList\WalletApp;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Logger\PrivateTokenMasker;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;
use ForumPay\PaymentGateway\PHPClient\Http\Exception\ApiExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class GetWalletApps implements GetWalletAppsInterface
{
    /**
     * ForumPay payment model
     *
     * @var ForumPay
     */
    private ForumPay $forumPay;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     */
    public function __construct(
        ForumPay $forumPay,
        ForumPayLogger $logger
    ) {
        $this->forumPay = $forumPay;
        $this->logger = $logger;
        $this->logger->addParser(new PrivateTokenMasker());
    }

    /**
     * @inheritdoc
     *
     * @return \ForumPay\PaymentGateway\Api\Data\WalletAppListInterface
     * @throws \Exception
     */
    public function getWalletApps(): \ForumPay\PaymentGateway\Api\Data\WalletAppListInterface
    {
        try {
            $this->logger->info('GetWalletApps entrypoint called.');
            $response = $this->forumPay->getWalletApps();

            /** @var WalletApp[] $walletAppDtos */
            $walletAppDtos = [];

            /** @var \ForumPay\PaymentGateway\PHPClient\Response\GetWalletApps\WalletApp $walletApp */
            foreach ($response->getWalletApps() as $walletApp) {
                $walletAppDto = new WalletApp(
                    $walletApp->getId(),
                    $walletApp->getName(),
                    $walletApp->getImage(),
                    $walletApp->getImageDarkmode()
                );
                $walletAppDtos[] = $walletAppDto;
            }

            $this->logger->debug('GetWalletApps response.', ['response' => $walletAppDtos]);
            $this->logger->info('GetWalletApps entrypoint finished.');

            return new WalletAppList($walletAppDtos);
        } catch (ApiExceptionInterface $e) {
            $this->logger->logApiException($e);
            throw new ApiHttpException($e, 1050);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                1100,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
