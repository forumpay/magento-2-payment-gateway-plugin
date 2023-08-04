<?php

namespace ForumPay\PaymentGateway\Model\Data\PaymentDetails;

use ForumPay\PaymentGateway\Api\Data\PaymentDetails\UnderpaymentInterface;

/**
 * @inheritdoc
 */
class Underpayment implements UnderpaymentInterface
{
    /**
     * @var string
     */
    private string $address;

    /**
     * @var string
     */
    private string $missingAmount;

    /**
     * @var string
     */
    private string $qr;

    /**
     * @var string
     */
    private string $qrAlt;

    /**
     * @var string
     */
    private string $qrImg;

    /**
     * @var string
     */
    private string $qrAltImg;

    /**
     * Underpayment DTO constructor
     *
     * @param string $address
     * @param string $missingAmount
     * @param string $qr
     * @param string $qrAlt
     * @param string $qrImg
     * @param string $qrAltImg
     */
    public function __construct(
        string $address,
        string $missingAmount,
        string $qr,
        string $qrAlt,
        string $qrImg,
        string $qrAltImg
    ) {
        $this->address = $address;
        $this->missingAmount = $missingAmount;
        $this->qr = $qr;
        $this->qrAlt = $qrAlt;
        $this->qrImg = $qrImg;
        $this->qrAltImg = $qrAltImg;
    }

    /**
     * @inheritdoc
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @inheritdoc
     */
    public function getMissingAmount(): string
    {
        return $this->missingAmount;
    }

    /**
     * @inheritdoc
     */
    public function getQr(): string
    {
        return $this->qr;
    }

    /**
     * @inheritdoc
     */
    public function getQrAlt(): string
    {
        return $this->qrAlt;
    }

    /**
     * @inheritdoc
     */
    public function getQrImg(): string
    {
        return $this->qrImg;
    }

    /**
     * @inheritdoc
     */
    public function getQrAltImg(): string
    {
        return $this->qrAltImg;
    }
}
