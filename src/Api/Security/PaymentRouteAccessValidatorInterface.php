<?php

namespace ForumPay\PaymentGateway\Api\Security;

/**
 * Validates caller access to ForumPay payment gateway routes.
 */
interface PaymentRouteAccessValidatorInterface
{
    /**
     * Require an active checkout quote (pre-place-order).
     *
     * @return void
     */
    public function assertActiveCheckoutQuote(): void;

    /**
     * Require a placed order in the checkout session.
     *
     * @return void
     */
    public function assertSessionOrder(): void;

    /**
     * Require that the payment belongs to the current checkout session order.
     *
     * @param string $paymentId
     * @return void
     */
    public function assertPaymentOwnership(string $paymentId): void;
}
