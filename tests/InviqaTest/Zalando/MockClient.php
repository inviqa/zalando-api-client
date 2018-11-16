<?php

namespace InviqaTest\Zalando;

use GuzzleHttp\Psr7\Response;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Request\ArticlePriceUpdateRequest;
use Inviqa\Zalando\Api\Response\ClientResponse;
use Inviqa\Zalando\Api\ZalandoConfiguration;
use RuntimeException;
use Teapot\StatusCode;

class MockClient implements Client
{
    private const AUTHENTICATION_SUCCESS_JSON = <<< 'EOT'
{
    "access_token": "f5f439951cf41034240e4fc14dc75c03",
    "expires_in": 7200,
    "not-before-policy": 0,
    "refresh_expires_in": 36000,
    "refresh_token": "f9847724f723092e2d8c71e27fdb59d8",
    "scope": "profile email",
    "session_state": "bc2d33be-d869-47bb-aebd-590b97a75a9f",
    "token_type": "bearer"
}
EOT;

    /**
     * @var ZalandoConfiguration
     */
    private $configuration;

    /**
     * @var string
     */
    private $failOnApiCall = '';

    public function __construct(ZalandoConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function authenticate(): ClientResponse
    {
        if (__FUNCTION__ === $this->failOnApiCall) {
            throw new RuntimeException('Invalid credentials');
        }

        return new ClientResponse(new Response(StatusCode::OK, [], self::AUTHENTICATION_SUCCESS_JSON));
    }

    public function updateArticlePrice(ArticlePriceUpdateRequest $request): ClientResponse
    {
        if (__FUNCTION__ === $this->failOnApiCall) {
            throw new RuntimeException('Failed to update article price');
        }

        return new ClientResponse(new Response(StatusCode::OK, [], ''));
    }

    public function setFailOnApiCall(string $failOnApiCall): void
    {
        $this->failOnApiCall = $failOnApiCall;
    }
}
