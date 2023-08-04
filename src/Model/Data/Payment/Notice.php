<?php

namespace ForumPay\PaymentGateway\Model\Data\Payment;

use ForumPay\PaymentGateway\Api\Data\Payment\NoticeInterface;

/**
 * @inheritdoc
 */
class Notice implements NoticeInterface
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $message;

    /**
     * Notice DTO constructor
     *
     * @param string $code
     * @param string $message
     */
    public function __construct(
        string $code,
        string $message
    ) {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
