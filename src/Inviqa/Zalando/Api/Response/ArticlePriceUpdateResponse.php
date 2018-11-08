<?php

namespace Inviqa\Zalando\Api\Response;

class ArticlePriceUpdateResponse
{
    /**
     * @var string
     */
    private $rawRequest;

    public function __construct(string $rawRequest)
    {
        $this->rawRequest = $rawRequest;
    }

    public function getRawRequest(): string
    {
        return $this->rawRequest;
    }
}
