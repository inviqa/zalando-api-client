<?php

namespace spec\Inviqa\Zalando\Api\Client;

use GuzzleHttp\ClientInterface;
use Inviqa\Zalando\Api\Client\ApiClient;
use Inviqa\Zalando\Api\Client\ClientFactory;
use Inviqa\Zalando\Api\Configuration;
use InviqaTest\Zalando\MockClient;
use InviqaTest\Zalando\TestConfiguration;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ClientFactory
 */
class ClientFactorySpec extends ObjectBehavior
{
    function let(Configuration $configuration)
    {
        $this->beConstructedWith($configuration);
    }

    function it_returns_a_mock_client_when_the_test_mode_is_enabled()
    {
        $configuration = new TestConfiguration();

        $this->beConstructedWith($configuration);

        $this->createClient()->shouldReturnAnInstanceOf(MockClient::class);
    }

    function it_returns_an_api_client_when_the_test_mode_is_not_enabled()
    {
        $configuration = new TestConfiguration(false);

        $this->beConstructedWith($configuration);

        $this->createClient()->shouldReturnAnInstanceOf(ApiClient::class);
    }
}
