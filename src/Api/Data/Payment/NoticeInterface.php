<?php

namespace ForumPay\PaymentGateway\Api\Data\Payment;

/**
 * Dto of payment notice
 */
interface NoticeInterface
{
    /**
     * Get notice identification code
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Get notice message
     *
     * @return string
     */
    public function getMessage(): string;
}
