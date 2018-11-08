<?php

namespace Inviqa\Zalando;

use Inviqa\Zalando\Api\ArticlePriceUpdater;
use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
use Inviqa\Zalando\Api\Writer\JsonFileWriterFactory;

class Application
{
    /**
     * @var ArticlePriceUpdater
     */
    private $articlePriceUpdater;

    public function __construct()
    {
        $this->articlePriceUpdater = new ArticlePriceUpdater(JsonFileWriterFactory::create());
    }

    public function updateArticlePrice(ArticlePrice $articlePrice): ArticlePriceUpdateResponse
    {
        return $this->articlePriceUpdater->updateArticlePrice($articlePrice);
    }
}
