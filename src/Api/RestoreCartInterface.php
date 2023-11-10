<?php

namespace ForumPay\PaymentGateway\Api;

/**
 * Interface for restoring the cart
 */
interface RestoreCartInterface
{
    /**
     * Returns last order items in shopping cart
     *
     * @return void
     */
    public function restoreCart(): void;
}
