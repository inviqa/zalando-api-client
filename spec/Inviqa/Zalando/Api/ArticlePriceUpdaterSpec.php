<?php

namespace spec\Inviqa\Zalando\Api;

use Inviqa\Zalando\Api\ArticlePriceUpdater;
use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Writer\JsonFileWriter;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticlePriceUpdater
 */
class ArticlePriceUpdaterSpec extends ObjectBehavior
{
    function let(JsonFileWriter $writer)
    {
        $this->beConstructedWith($writer);
    }

    function it_writes_article_price_update_json_string_to_a_file_in_memory(JsonFileWriter $writer)
    {
        $articlePrice = new ArticlePrice(34.99, 'blue-shoes', '9780679762881', 'bf48ba35-149d-4b76-8ac9-d08d126b517f');
        $data = [
            'price' => [
                'regular_price' => 34.99,
                'currency' => 'EUR',
                'vat_code' => '2',
            ],
            'merchant_simple_id' => 'blue-shoes',
            'ean' => '9780679762881',
            'sales_channel_id' => 'bf48ba35-149d-4b76-8ac9-d08d126b517f',
            'fulfillment_type' => 'fulfilled_by_zalando',
        ];

        $writer->write($data)->willReturn('raw json request');

        $response = $this->updateArticlePrice($articlePrice);

        $response->getRawRequest()->shouldReturn('raw json request');
    }
}
