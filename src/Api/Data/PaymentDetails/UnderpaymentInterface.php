<?php

namespace ForumPay\PaymentGateway\Api\Data\PaymentDetails;

/**
 * Dto of payment underpayment
 */
interface UnderpaymentInterface
{
    /**
     * Get payment address connected to the payment id
     *
     * @return string
     */
    public function getAddress(): string;

    /**
     * Amount missing from initial payment to complete payment.
     *
     * @return string
     */
    public function getMissingAmount(): string;

    /**
     * String for qr code that includes cryptocurrency, address and amount
     *
     * @return string
     */
    public function getQr(): string;

    /**
     * Alternative string for qr code for legacy wallets including address only
     *
     * @return string
     */
    public function getQrAlt(): string;

    /**
     * URL for qr code image download
     *
     * @return string
     */
    public function getQrImg(): string;

    /**
     * URL for qr_alt code image download
     *
     * @return string
     */
    public function getQrAltImg(): string;
}
