<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\PaymentInterface;
use ForumPay\PaymentGateway\Model\Data\Payment\Notice;

/**
 * @inheritdoc
 */
class Payment implements PaymentInterface
{
    /**
     * @var string
     */
    private string $paymentId;

    /**
     * @var string
     */
    private string $address;

    /**
     * @var string
     */
    private string $missingAmount;

    /**
     * @var int
     */
    private int $minConfirmations;

    /**
     * @var string|null
     */
    private ?string $fastTransactionFee;

    /**
     * @var string|null
     */
    private ?string $fastTransactionFeeCurrency;

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
     * @var Notice[]
     */
    private array $notices;

    /**
     * Payment DTO constructor
     *
     * @param string $paymentId
     * @param string $address
     * @param string $missingAmount
     * @param int $minConfirmations
     * @param string|null $fastTransactionFee
     * @param string|null $fastTransactionFeeCurrency
     * @param string $qr
     * @param string $qrAlt
     * @param string $qrImg
     * @param string $qrAltImg
     * @param array $notices
     */
    public function __construct(
        string $paymentId,
        string $address,
        string $missingAmount,
        int $minConfirmations,
        ?string $fastTransactionFee,
        ?string $fastTransactionFeeCurrency,
        string $qr,
        string $qrAlt,
        string $qrImg,
        string $qrAltImg,
        array $notices = []
    ) {
        $this->paymentId = $paymentId;
        $this->address = $address;
        $this->missingAmount = $missingAmount;
        $this->minConfirmations = $minConfirmations;
        $this->fastTransactionFee = $fastTransactionFee;
        $this->fastTransactionFeeCurrency = $fastTransactionFeeCurrency;
        $this->qr = $qr;
        $this->qrAlt = $qrAlt;
        $this->qrImg = $qrImg;
        $this->qrAltImg = $qrAltImg;
        $this->notices = $notices;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
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
    public function getMinConfirmations(): int
    {
        return $this->minConfirmations;
    }

    /**
     * @inheritdoc
     */
    public function getFastTransactionFee(): ?string
    {
        return $this->fastTransactionFee;
    }

    /**
     * @inheritdoc
     */
    public function getFastTransactionFeeCurrency(): ?string
    {
        return $this->fastTransactionFeeCurrency;
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

    /**
     * @inheritdoc
     */
    public function getNotices(): array
    {
        return $this->notices;
    }
}
