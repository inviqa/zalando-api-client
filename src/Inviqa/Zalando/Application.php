<?php

namespace Inviqa\Zalando;

use Inviqa\Zalando\Api\ArticlePrice\ArticlePrice;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;

class Application
{
    public function createArticlePriceUpdateRequest(
        ArticlePrice $price,
        MerchantOperationMetadata $metadata
    ): ArticlePriceUpdateRequest {
        return new ArticlePriceUpdateRequest($price, $metadata);
    }
}
