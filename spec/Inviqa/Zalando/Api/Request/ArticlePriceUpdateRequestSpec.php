<?php

namespace spec\Inviqa\Zalando\Api\Request;

use Inviqa\Zalando\Api\Article\ArticlePrice;
use Inviqa\Zalando\Api\Article\ArticleReference;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticlePriceUpdateRequest
 */
class ArticlePriceUpdateRequestSpec extends ObjectBehavior
{
    function it_builds_and_gets_the_raw_article_price_update_request()
    {
        $price = new ArticlePrice(new ArticleReference('blue-shoes', '9780679762881'), 34.99, 'EUR', '2');
        $metadata = new MerchantOperationMetadata('bf48ba35-149d-4b7', MerchantOperationMetadata::FULFILLED_BY_ZALANDO);
        $json = json_encode([
            'price' => [
                'regular_price' => 34.99,
                'currency' => 'EUR',
                'vat_code' => '2',
            ],
            'merchant_simple_id' => 'blue-shoes',
            'ean' => '9780679762881',
            'sales_channel_id' => 'bf48ba35-149d-4b7',
            'fulfillment_type' => MerchantOperationMetadata::FULFILLED_BY_ZALANDO,
        ]);

        $this->beConstructedWith($price, $metadata);

        $this->getRawRequest()->shouldReturn($json);
    }
}
