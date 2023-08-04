<?php

namespace ForumPay\PaymentGateway\Model\Data;

use ForumPay\PaymentGateway\Api\Data\PaymentDetailsInterface;
use ForumPay\PaymentGateway\Model\Data\PaymentDetails\Underpayment;

/**
 * @inheritdoc
 */
class PaymentDetails implements PaymentDetailsInterface
{
    /**
     * @var string|null
     */
    private ?string $referenceNo;

    /**
     * @var string
     */
    private string $inserted;

    /**
     * @var string|null
     */
    private ?string $invoiceAmount;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $invoiceCurrency;

    /**
     * @var string|null
     */
    private ?string $amount;

    /**
     * @var int
     */
    private int $minConfirmations;

    /**
     * @var bool
     */
    private bool $acceptZeroConfirmations;

    /**
     * @var bool
     */
    private bool $requireKytForConfirmation;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var bool
     */
    private bool $confirmed;

    /**
     * @var string|null
     */
    private ?string $confirmedTime;

    /**
     * @var string|null
     */
    private ?string $reason;

    /**
     * @var string|null
     */
    private ?string $payment;

    /**
     * @var string|null
     */
    private ?string $sid;

    /**
     * @var string
     */
    private string $confirmations;

    /**
     * @var string|null
     */
    private ?string $accessToken;

    /**
     * @var string|null
     */
    private ?string $accessUrl;

    /**
     * @var string|null
     */
    private ?string $waitTime;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var bool
     */
    private bool $cancelled;

    /**
     * @var string|null
     */
    private ?string $cancelledTime;

    /**
     * @var string|null
     */
    private ?string $printString;

    /**
     * @var string
     */
    private string $state;

    /**
     * @var Underpayment|null
     */
    private ?Underpayment $underpayment;

    /**
     * PaymentDetails DTO constructor
     *
     * @param string|null $referenceNo
     * @param string $inserted
     * @param string|null $invoiceAmount
     * @param string $type
     * @param string $invoiceCurrency
     * @param string|null $amount
     * @param int $minConfirmations
     * @param bool $acceptZeroConfirmations
     * @param bool $requireKytForConfirmation
     * @param string $currency
     * @param bool $confirmed
     * @param string|null $confirmedTime
     * @param string|null $reason
     * @param string|null $payment
     * @param string|null $sid
     * @param string $confirmations
     * @param string|null $accessToken
     * @param string|null $accessUrl
     * @param string|null $waitTime
     * @param string $status
     * @param bool $cancelled
     * @param string|null $cancelledTime
     * @param string|null $printString
     * @param string $state
     * @param Underpayment|null $underpayment
     */
    public function __construct(
        ?string $referenceNo,
        string $inserted,
        ?string $invoiceAmount,
        string $type,
        string $invoiceCurrency,
        ?string $amount,
        int $minConfirmations,
        bool $acceptZeroConfirmations,
        bool $requireKytForConfirmation,
        string $currency,
        bool $confirmed,
        ?string $confirmedTime,
        ?string $reason,
        ?string $payment,
        ?string $sid,
        string $confirmations,
        ?string $accessToken,
        ?string $accessUrl,
        ?string $waitTime,
        string $status,
        bool $cancelled,
        ?string $cancelledTime,
        ?string $printString,
        string $state,
        ?Underpayment $underpayment = null
    ) {
        $this->referenceNo = $referenceNo;
        $this->inserted = $inserted;
        $this->invoiceAmount = $invoiceAmount;
        $this->type = $type;
        $this->invoiceCurrency = $invoiceCurrency;
        $this->amount = $amount;
        $this->minConfirmations = $minConfirmations;
        $this->acceptZeroConfirmations = $acceptZeroConfirmations;
        $this->requireKytForConfirmation = $requireKytForConfirmation;
        $this->currency = $currency;
        $this->confirmed = $confirmed;
        $this->confirmedTime = $confirmedTime;
        $this->reason = $reason;
        $this->payment = $payment;
        $this->sid = $sid;
        $this->confirmations = $confirmations;
        $this->accessToken = $accessToken;
        $this->accessUrl = $accessUrl;
        $this->waitTime = $waitTime;
        $this->status = $status;
        $this->cancelled = $cancelled;
        $this->cancelledTime = $cancelledTime;
        $this->printString = $printString;
        $this->state = $state;
        $this->underpayment = $underpayment;
    }

    /**
     * @inheritdoc
     */
    public function getReferenceNo(): ?string
    {
        return $this->referenceNo;
    }

    /**
     * @inheritdoc
     */
    public function getInserted(): string
    {
        return $this->inserted;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceCurrency(): string
    {
        return $this->invoiceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function getAmount(): ?string
    {
        return $this->amount;
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
    public function isAcceptZeroConfirmations(): bool
    {
        return $this->acceptZeroConfirmations;
    }

    /**
     * @inheritdoc
     */
    public function isRequireKytForConfirmation(): bool
    {
        return $this->requireKytForConfirmation;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmedTime(): ?string
    {
        return $this->confirmedTime;
    }

    /**
     * @inheritdoc
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @inheritdoc
     */
    public function getPayment(): ?string
    {
        return $this->payment;
    }

    /**
     * @inheritdoc
     */
    public function getSid(): ?string
    {
        return $this->sid;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmations(): string
    {
        return $this->confirmations;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getAccessUrl(): ?string
    {
        return $this->accessUrl;
    }

    /**
     * @inheritdoc
     */
    public function getWaitTime(): ?string
    {
        return $this->waitTime;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * @inheritdoc
     */
    public function getCancelledTime(): ?string
    {
        return $this->cancelledTime;
    }

    /**
     * @inheritdoc
     */
    public function getPrintString(): ?string
    {
        return $this->printString;
    }

    /**
     * @inheritdoc
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function getUnderpayment(): ?Underpayment
    {
        return $this->underpayment;
    }
}
