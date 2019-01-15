<?php

namespace spec\Inviqa\Zalando\Api\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Client\ApiClient;
use Inviqa\Zalando\Api\Response\ClientResponse;
use Inviqa\Zalando\Api\ZalandoConfiguration;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;

/**
 * @mixin ApiClient
 */
class ApiClientSpec extends ObjectBehavior
{
    function let(ZalandoConfiguration $configuration, ClientInterface $client)
    {
        $configuration->getAuthenticationEndpointUrl()
            ->willReturn('https://api-sandbox.merchants.zalando.com/auth/token');
        $configuration->getUsername()->willReturn('testuser');
        $configuration->getSecret()->willReturn('secret123');

        $this->beConstructedWith($configuration, $client);
    }

    function it_is_a_client()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_authenticates_and_returns_a_client_response(
        ClientInterface $client,
        ResponseInterface $response,
        StreamInterface $body
    ) {
        $options = [
            RequestOptions::AUTH => ['testuser', 'secret123'],
            RequestOptions::FORM_PARAMS => ['grant_type' => 'client_credentials'],
        ];

        $client->request('POST', 'https://api-sandbox.merchants.zalando.com/auth/token', $options)
            ->willReturn($response);
        $response->getStatusCode()->willReturn(StatusCode::OK);
        $response->getHeaderLine('Date')->willReturn('Mon, 07 Jan 2019 15:47:53 GMT');
        $response->getBody()->willReturn($body);
        $body->getContents()->willReturn('{"access_token":"abc123","expires_in":7200}');

        $this->authenticate()->shouldReturnAnInstanceOf(ClientResponse::class);
    }
}
