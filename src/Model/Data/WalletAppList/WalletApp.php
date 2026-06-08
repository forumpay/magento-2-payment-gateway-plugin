<?php

namespace ForumPay\PaymentGateway\Model\Data\WalletAppList;

use ForumPay\PaymentGateway\Api\Data\WalletAppList\WalletAppInterface;

/**
 * @inheritdoc
 */
class WalletApp implements WalletAppInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $image;

    /**
     * @var string|null
     */
    private ?string $imageDarkmode;

    /**
     * WalletApp DTO constructor
     *
     * @param string $id
     * @param string $name
     * @param string|null $image
     * @param string|null $imageDarkmode
     */
    public function __construct(
        string $id,
        string $name,
        ?string $image,
        ?string $imageDarkmode
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->imageDarkmode = $imageDarkmode;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @inheritdoc
     */
    public function getImageDarkmode(): ?string
    {
        return $this->imageDarkmode;
    }
}
