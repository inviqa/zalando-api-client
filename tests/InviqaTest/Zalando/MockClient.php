<?php

namespace InviqaTest\Zalando;

use GuzzleHttp\Psr7\Response;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Configuration;
use Inviqa\Zalando\Api\Response\ClientResponse;

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
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function authenticate(): ClientResponse
    {
        return new ClientResponse(new Response(200, [], self::AUTHENTICATION_SUCCESS_JSON));
    }
}
