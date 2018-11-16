<?php

namespace spec\Inviqa\Zalando\Api\Article;

use Inviqa\Zalando\Api\Article\ArticlePrice;
use Inviqa\Zalando\Api\Article\ArticlePriceUpdater;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Response\ClientResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ArticlePriceUpdater
 */
class ArticlePriceUpdaterSpec extends ObjectBehavior
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_updates_the_article_price(
        Client $client,
        ArticlePrice $price,
        MerchantOperationMetadata $metadata,
        ClientResponse $response
    ) {
        $client->updateArticlePrice(Argument::type(ArticlePriceUpdateRequest::class))->willReturn($response);

        $this->updateArticlePrice($price, $metadata)->shouldReturn($response);
    }
}
