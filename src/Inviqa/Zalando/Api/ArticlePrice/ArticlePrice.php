<?php

namespace Inviqa\Zalando\Api\ArticlePrice;

use Inviqa\Zalando\Api\Article\ArticleReference;

class ArticlePrice
{
    /**
     * @var ArticleReference
     */
    private $articleReference;

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

    public function __construct(
        ArticleReference $articleReference,
        float $regularPrice,
        string $currency,
        string $vatCode
    ) {
        $this->articleReference = $articleReference;
        $this->regularPrice = $regularPrice;
        $this->currency = $currency;
        $this->vatCode = $vatCode;
    }

    public function getArticleReference(): ArticleReference
    {
        return $this->articleReference;
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
}
