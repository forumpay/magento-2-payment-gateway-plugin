<?php

namespace ForumPay\PaymentGateway\Model;

use ForumPay\PaymentGateway\Api\RestoreCartInterface;
use ForumPay\PaymentGateway\Model\Logger\ForumPayLogger;
use ForumPay\PaymentGateway\Model\Payment\ForumPay;

class RestoreCart implements RestoreCartInterface
{
    /**
     * ForumPay payment model
     *
     * @var ForumPay
     */
    private ForumPay $forumPay;

    /**
     * @var ForumPayLogger
     */
    private ForumPayLogger $logger;

    /**
     * Constructor
     *
     * @param ForumPay $forumPay
     * @param ForumPayLogger $logger
     */
    public function __construct(ForumPay $forumPay, ForumPayLogger $logger)
    {
        $this->forumPay = $forumPay;
        $this->logger = $logger;
    }

    /**
     * Returns last order items in shopping cart
     *
     * @throws \Exception
     */
    public function restoreCart(): void
    {
        try {
            $this->logger->info('RestoreCart entrypoint called.');

            $this->forumPay->restoreCart();

            $this->logger->info('RestoreCart entrypoint finished.');
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                7050,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
    }
}
