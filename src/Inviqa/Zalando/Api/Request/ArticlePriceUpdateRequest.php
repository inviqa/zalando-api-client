<?php

namespace Inviqa\Zalando\Api\Request;

use Inviqa\Zalando\Api\Article\ArticlePrice;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;

class ArticlePriceUpdateRequest
{
    /**
     * @var ArticlePrice
     */
    private $price;

    /**
     * @var MerchantOperationMetadata
     */
    private $metadata;

    public function __construct(ArticlePrice $price, MerchantOperationMetadata $metadata)
    {
        $this->price = $price;
        $this->metadata = $metadata;
    }

    public function getRawRequest(): string
    {
        return json_encode($this->build());
    }

    private function build(): array
    {
        $articleReference = $this->price->getArticleReference();

        return [
            'price' => [
                'regular_price' => $this->price->getRegularPrice(),
                'currency' => $this->price->getCurrency(),
                'vat_code' => $this->price->getVatCode(),
            ],
            'merchant_simple_id' => $articleReference->getMerchantSimpleId(),
            'ean' => $articleReference->getEan(),
            'sales_channel_id' => $this->metadata->getSalesChannelId(),
            'fulfillment_type' => $this->metadata->getFulfillmentType(),
        ];
    }
}
