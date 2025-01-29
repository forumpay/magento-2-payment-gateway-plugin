<?php

namespace ForumPay\PaymentGateway\Model\Data\Payer;

/**
 * Dto of payer
 */
abstract class Payer
{
    /**
     * Converts the Payer DTO to an array representation
     *
     * @return array
     */
    abstract public function toArray(): array;
}
