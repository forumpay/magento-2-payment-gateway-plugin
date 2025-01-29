<?php

namespace ForumPay\PaymentGateway\Api\Data\Payer;

use ForumPay\PaymentGateway\Model\Data\Payer\Payer;

/**
 *
 * Dto for payer factory
 */
interface PayerFactoryInterface
{
    /**
     * Create an instance of the payer DTO
     *
     * @param array $payer
     * @return Payer|null
     */
    public function create(array $payer): ?Payer;
}
