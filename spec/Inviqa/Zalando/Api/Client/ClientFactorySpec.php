<?php

namespace spec\Inviqa\Zalando\Api\Client;

use Inviqa\Zalando\Api\Client\ApiClient;
use Inviqa\Zalando\Api\Client\ClientFactory;
use Inviqa\Zalando\Api\ZalandoConfiguration;
use InviqaTest\Zalando\MockClient;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ClientFactory
 */
class ClientFactorySpec extends ObjectBehavior
{
    function let(ZalandoConfiguration $configuration)
    {
        $this->beConstructedWith($configuration);
    }

    function it_returns_a_mock_client_when_the_test_mode_is_enabled(ZalandoConfiguration $configuration)
    {
        $configuration->isTestMode()->willReturn(true);

        $this->createClient()->shouldReturnAnInstanceOf(MockClient::class);
    }

    function it_returns_an_api_client_when_the_test_mode_is_not_enabled(ZalandoConfiguration $configuration)
    {
        $configuration->isTestMode()->willReturn(false);

        $this->createClient()->shouldReturnAnInstanceOf(ApiClient::class);
    }
}
