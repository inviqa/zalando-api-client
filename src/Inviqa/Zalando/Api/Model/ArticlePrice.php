<?php

namespace Inviqa\Zalando\Api\Model;

use InvalidArgumentException;

class ArticlePrice
{
    const CURRENCY_EUR = 'EUR';
    const VAT_CODE = '2';
    const FULFILLED_BY_MERCHANT = 'fulfilled_by_merchant';
    const FULFILLED_BY_ZALANDO = 'fulfilled_by_zalando';
    const ALL_CURRENCIES = [
        self::CURRENCY_EUR,
    ];
    const ALL_VAT_CODES = [
        self::VAT_CODE,
    ];
    const ALL_FULFILLED_BY = [
        self::FULFILLED_BY_MERCHANT,
        self::FULFILLED_BY_ZALANDO,
    ];

    /**
     * @var float
     */
    private $regularPrice;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $vatCode;

    /**
     * @var string
     */
    private $merchantSimpleId;

    /**
     * @var string
     */
    private $ean;

    /**
     * @var string
     */
    private $salesChannelId;

    /**
     * @var string
     */
    private $fulfillmentType;

    public function __construct(
        float $regularPrice,
        string $merchantSimpleId,
        string $ean,
        string $salesChannelId,
        string $currency = self::CURRENCY_EUR,
        string $vatCode = self::VAT_CODE,
        string $fulfillmentType = self::FULFILLED_BY_ZALANDO
    ) {
        $this->regularPrice = $regularPrice;
        $this->merchantSimpleId = $merchantSimpleId;
        $this->ean = $ean;
        $this->salesChannelId = $salesChannelId;
        $this->currency = $this->assertCurrency($currency);
        $this->vatCode = $this->assertVatCode($vatCode);
        $this->fulfillmentType = $this->assertFulfillmentType($fulfillmentType);
    }

    public function getRegularPrice(): float
    {
        return $this->regularPrice;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getVatCode(): string
    {
        return $this->vatCode;
    }

    public function getMerchantSimpleId(): string
    {
        return $this->merchantSimpleId;
    }

    public function getEan(): string
    {
        return $this->ean;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getFulfillmentType(): string
    {
        return $this->fulfillmentType;
    }

    private function assertCurrency(string $currency): string
    {
        if (!in_array($currency, self::ALL_CURRENCIES)) {
            throw new InvalidArgumentException(sprintf('Unknown currency "%s"', $currency));
        }

        return $currency;
    }

    private function assertVatCode(string $vatCode): string
    {
        if (!in_array($vatCode, self::ALL_VAT_CODES)) {
            throw new InvalidArgumentException(sprintf('Unknown VAT code "%s"', $vatCode));
        }

        return $vatCode;
    }

    private function assertFulfillmentType(string $fulfillmentType): string
    {
        if (!in_array($fulfillmentType, self::ALL_FULFILLED_BY)) {
            throw new InvalidArgumentException(sprintf('Unknown fulfillment type "%s"', $fulfillmentType));
        }

        return $fulfillmentType;
    }
}
