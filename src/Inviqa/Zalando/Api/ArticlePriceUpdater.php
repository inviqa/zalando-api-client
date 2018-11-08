<?php

namespace Inviqa\Zalando\Api;

use Inviqa\Zalando\Api\Model\ArticlePrice;
use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
use Inviqa\Zalando\Api\Writer\JsonFileWriter;

class ArticlePriceUpdater
{
    /**
     * @var JsonFileWriter
     */
    private $writer;

    public function __construct(JsonFileWriter $writer)
    {
        $this->writer = $writer;
    }

    public function updateArticlePrice(ArticlePrice $articlePrice): ArticlePriceUpdateResponse
    {
        $data = [
            'price' => [
                'regular_price' => $articlePrice->getRegularPrice(),
                'currency' => $articlePrice->getCurrency(),
                'vat_code' => $articlePrice->getVatCode(),
            ],
            'merchant_simple_id' => $articlePrice->getMerchantSimpleId(),
            'ean' => $articlePrice->getEan(),
            'sales_channel_id' => $articlePrice->getSalesChannelId(),
            'fulfillment_type' => 'fulfilled_by_zalando',
        ];

        $rawRequest = $this->writer->write($data);

        return new ArticlePriceUpdateResponse($rawRequest);
    }
}
