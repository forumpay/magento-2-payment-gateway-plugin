<?php

namespace ForumPay\PaymentGateway\Api\Data\Payment;

/**
 * Dto of payment beneficiary vasp details
 */
interface BeneficiaryVaspDetailsInterface
{
    /**
     * Return beneficiary name
     *
     * @return string
     */
    public function getBeneficiaryName(): string;

    /**
     * Return beneficiary vasp
     *
     * @return string
     */
    public function getBeneficiaryVasp(): string;

    /**
     * Return beneficiary vasp did
     *
     * @return string|null
     */
    public function getBeneficiaryVaspDid(): ?string;
}
