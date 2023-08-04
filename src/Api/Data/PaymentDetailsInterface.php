<?php

namespace ForumPay\PaymentGateway\Api\Data;

/**
 * Dto for detailed payment information
 */
interface PaymentDetailsInterface
{
    /**
     *  Payment reference number, Magento incrementalID is sent in start payment
     *
     * @return string|null
     */
    public function getReferenceNo(): ?string;

    /**
     * Date and time of the transaction
     *
     * @return string
     */
    public function getInserted(): string;

    /**
     * Amount on invoice for FIAT currency
     *
     * @return string|null
     */
    public function getInvoiceAmount(): ?string;

    /**
     * Type of order
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Currency code for FIAT currency on invoice (EUR, USD, etc.)
     *
     * @return string
     */
    public function getInvoiceCurrency(): string;

    /**
     * Total amount to pay
     *
     * @return string|null
     */
    public function getAmount(): ?string;

    /**
     * Minimum confirmations to wait (this is informal data. Always wait till confirmed=true)
     *
     * @return int
     */
    public function getMinConfirmations(): int;

    /**
     * Return true if accepts zero confirmation
     *
     * @return bool
     */
    public function isAcceptZeroConfirmations(): bool;

    /**
     * Require KYT check to succeed before confirming the payment
     *
     * @return bool
     */
    public function isRequireKytForConfirmation(): bool;

    /**
     * Cryptocurrency symbol (BTC, ETH..) to exchange the fiat into
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Is transaction successful and confirmed
     *
     * @return bool
     */
    public function isConfirmed(): bool;

    /**
     * Time of confirmation
     *
     * @return string|null
     */
    public function getConfirmedTime(): ?string;

    /**
     * Consumer's reason for payment cancellation
     *
     * @return string|null
     */
    public function getReason(): ?string;

    /**
     * Received amount of payment
     *
     * @return string|null
     */
    public function getPayment(): ?string;

    /**
     * Sub Account ID
     *
     * @return string|null
     */
    public function getSid(): ?string;

    /**
     * Received confirmations of payment
     *
     * @return string
     */
    public function getConfirmations(): string;

    /**
     * Access token
     *
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * Public URL for check payment preview
     *
     * @return string|null
     */
    public function getAccessUrl(): ?string;

    /**
     * Expected time to confirm
     *
     * @return string|null
     */
    public function getWaitTime(): ?string;

    /**
     * Payment status description for user
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Is cancellation successful
     *
     * @return bool
     */
    public function isCancelled(): bool;

    /**
     * Time of cancellation
     *
     * @return string|null
     */
    public function getCancelledTime(): ?string;

    /**
     * Status in human-readable string
     *
     * @return string|null
     */
    public function getPrintString(): ?string;

    /**
     * Payment states: "waiting", "processing", "cancelled", "confirmed", "zero_confirmed", "underpayment", "blocked"
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Underpayment status of the payment
     *
     * @return \ForumPay\PaymentGateway\Api\Data\PaymentDetails\UnderpaymentInterface|null
     */
    public function getUnderpayment(): ?\ForumPay\PaymentGateway\Api\Data\PaymentDetails\UnderpaymentInterface;
}
