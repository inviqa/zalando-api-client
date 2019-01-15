<?php

namespace Inviqa\Zalando\Api\Merchant;

use InvalidArgumentException;

class MerchantOperationMetadata
{
    public const FULFILLED_BY_MERCHANT = 'fulfilled_by_merchant';
    public const FULFILLED_BY_ZALANDO = 'fulfilled_by_zalando';
    private const ALL_FULFILLED_BY = [
        self::FULFILLED_BY_MERCHANT,
        self::FULFILLED_BY_ZALANDO,
    ];

    /**
     * @var string
     */
    private $salesChannelId;

    /**
     * @var string
     */
    private $fulfillmentType;

    public function __construct(string $salesChannelId, string $fulfillmentType)
    {
        $this->validate($fulfillmentType);

        $this->salesChannelId = $salesChannelId;
        $this->fulfillmentType = $fulfillmentType;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getFulfillmentType(): string
    {
        return $this->fulfillmentType;
    }

    private function validate(string $fulfillmentType): void
    {
        if (!in_array($fulfillmentType, self::ALL_FULFILLED_BY)) {
            throw new InvalidArgumentException(sprintf('Unknown fulfillment type "%s"', $fulfillmentType));
        }
    }
}
