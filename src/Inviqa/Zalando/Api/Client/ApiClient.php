<?php

namespace Inviqa\Zalando\Api\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Configuration;
use Inviqa\Zalando\Api\Response\ClientResponse;

class ApiClient implements Client
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(Configuration $configuration, ClientInterface $client)
    {
        $this->configuration = $configuration;
        $this->client = $client;
    }

    public function authenticate(): ClientResponse
    {
        $response = $this->client->request('POST', $this->configuration->getAuthenticationEndpointUrl(), [
            RequestOptions::AUTH => [$this->configuration->getUsername(), $this->configuration->getSecret()],
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        return new ClientResponse($response);
    }
}
