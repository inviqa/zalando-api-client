<?php

namespace Inviqa\Zalando\Api;

use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Response\ClientResponse;

interface Client
{
    public function authenticate(): ClientResponse;

    public function updateArticlePrice(ArticlePriceUpdateRequest $request): ClientResponse;
}
