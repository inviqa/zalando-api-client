<?php

namespace Inviqa\Zalando\Api\Article;

class ArticleReference
{
    /**
     * @var string
     */
    private $merchantSimpleId;

    /**
     * @var string
     */
    private $ean;

    public function __construct(string $merchantSimpleId, string $ean)
    {
        $this->merchantSimpleId = $merchantSimpleId;
        $this->ean = $ean;
    }

    public function getMerchantSimpleId(): string
    {
        return $this->merchantSimpleId;
    }

    public function getEan(): string
    {
        return $this->ean;
    }
}
