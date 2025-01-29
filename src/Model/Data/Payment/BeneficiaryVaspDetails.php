<?php

namespace ForumPay\PaymentGateway\Model\Data\Payment;

use ForumPay\PaymentGateway\Api\Data\Payment\BeneficiaryVaspDetailsInterface;

/**
 * @inheritdoc
 */
class BeneficiaryVaspDetails implements BeneficiaryVaspDetailsInterface
{
    /**
     * @var string
     */
    private string $beneficiaryName;

    /**
     * @var string
     */
    private string $beneficiaryVasp;

    /**
     * @var string|null
     */
    private ?string $beneficiaryVaspDid;

    /**
     * Beneficiary vasp details DTO constructor
     *
     * @param string $beneficiaryName
     * @param string $beneficiaryVasp
     * @param string|null $beneficiaryVaspDid
     */
    public function __construct(
        string $beneficiaryName,
        string $beneficiaryVasp,
        ?string $beneficiaryVaspDid
    ) {
        $this->beneficiaryName = $beneficiaryName;
        $this->beneficiaryVasp = $beneficiaryVasp;
        $this->beneficiaryVaspDid = $beneficiaryVaspDid;
    }

    /**
     * @inheritdoc
     */
    public function getBeneficiaryName(): string
    {
        return $this->beneficiaryName;
    }

    /**
     * @inheritdoc
     */
    public function getBeneficiaryVasp(): string
    {
        return $this->beneficiaryVasp;
    }

    /**
     * @inheritdoc
     */
    public function getBeneficiaryVaspDid(): ?string
    {
        return $this->beneficiaryVaspDid;
    }
}
