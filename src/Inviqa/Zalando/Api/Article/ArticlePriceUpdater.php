<?php

namespace Inviqa\Zalando\Api\Article;

use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Response\ClientResponse;

class ArticlePriceUpdater
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function updateArticlePrice(ArticlePrice $price, MerchantOperationMetadata $metadata): ClientResponse
    {
        return $this->client->updateArticlePrice(new ArticlePriceUpdateRequest($price, $metadata));
    }
}
