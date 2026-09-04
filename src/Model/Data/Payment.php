<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\PaymentInterface;
use ForumPay\PaymentGateway\Model\Data\Payment\BeneficiaryVaspDetails;
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
     * @var string
     */
    private string $statsToken;

    /**
     * @var string|null
     */
    private ?string $wcToken;

    /**
     * @var BeneficiaryVaspDetails|null
     */
    private ?BeneficiaryVaspDetails $beneficiaryVaspDetails;

    /**
     * @var string|null
     */
    private ?string $itemName;

    /**
     * @var string|null
     */
    private ?string $invoiceSurchargeAmount;

    /**
     * @var string|null
     */
    private ?string $invoiceSurchargePercent;

    /**
     * @var string|null
     */
    private ?string $invoiceAmountWithSurcharge;

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
     * @param string $statsToken
     * @param string|null $wcToken
     * @param BeneficiaryVaspDetails|null $beneficiaryVaspDetails
     * @param string|null $itemName
     * @param string|null $invoiceSurchargeAmount
     * @param string|null $invoiceSurchargePercent
     * @param string|null $invoiceAmountWithSurcharge
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
        array $notices = [],
        string $statsToken = '',
        ?string $wcToken = null,
        ?BeneficiaryVaspDetails $beneficiaryVaspDetails = null,
        ?string $itemName = null,
        ?string $invoiceSurchargeAmount = null,
        ?string $invoiceSurchargePercent = null,
        ?string $invoiceAmountWithSurcharge = null
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
        $this->statsToken = $statsToken;
        $this->wcToken = $wcToken;
        $this->beneficiaryVaspDetails = $beneficiaryVaspDetails;
        $this->itemName = $itemName;
        $this->invoiceSurchargeAmount = $invoiceSurchargeAmount;
        $this->invoiceSurchargePercent = $invoiceSurchargePercent;
        $this->invoiceAmountWithSurcharge = $invoiceAmountWithSurcharge;
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

    /**
     * @inheritdoc
     */
    public function getStatsToken(): string
    {
        return $this->statsToken;
    }

    /**
     * @inheritdoc
     */
    public function getWcToken(): ?string
    {
        return $this->wcToken;
    }

    /**
     * @inheritdoc
     */
    public function getBeneficiaryVaspDetails(): ?BeneficiaryVaspDetails
    {
        return $this->beneficiaryVaspDetails;
    }

    /**
     * @inheritdoc
     */
    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceSurchargeAmount(): ?string
    {
        return $this->invoiceSurchargeAmount;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceSurchargePercent(): ?string
    {
        return $this->invoiceSurchargePercent;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceAmountWithSurcharge(): ?string
    {
        return $this->invoiceAmountWithSurcharge;
    }
}
