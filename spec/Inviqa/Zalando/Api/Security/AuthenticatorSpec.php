<?php

namespace spec\Inviqa\Zalando\Api\Security;

use DateInterval;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Exception\ZalandoException;
use Inviqa\Zalando\Api\Response\ClientResponse;
use Inviqa\Zalando\Api\Security\AuthenticationParameters;
use Inviqa\Zalando\Api\Security\AuthenticationStorage;
use Inviqa\Zalando\Api\Security\Authenticator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * @mixin Authenticator
 */
class AuthenticatorSpec extends ObjectBehavior
{
    function let(Client $client, AuthenticationStorage $storage)
    {
        $this->beConstructedWith($client, $storage);
    }

    function it_authenticates_then_stores_the_response_and_returns_the_authentication_parameters(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage
    ) {
        $authenticatedAt = new DateTimeImmutable('2019-01-07 15:47:53');
        $expiry = new DateTimeImmutable('2019-01-07 17:47:53');
        $newParameters = new AuthenticationParameters([
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ]);

        $storage->fetch()->willReturn(null);
        $storage->save($newParameters)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"abc123","expires_in":7200}');

        $result = $this->authenticate();

        $result->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $result->getExpiry()->shouldBeLike($expiry);
        $result->getAccessToken()->shouldReturn('abc123');
        $result->getExpiresIn()->shouldReturn(7200);
    }

    function it_knows_not_to_make_a_new_call_to_authenticate_and_to_use_the_previous_authentication_parameters(
        Client $client,
        AuthenticationStorage $storage,
        AuthenticationParameters $previousParameters
    ) {
        $storage->fetch()->willReturn($previousParameters);
        $storage->save(Argument::any())->shouldNotBeCalled();
        $previousParameters->hasAccessTokenExpired()->willReturn(false);
        $client->authenticate()->shouldNotBeCalled();

        $this->authenticate()->shouldReturn($previousParameters);
    }

    function it_knows_to_make_a_new_call_to_authenticate_and_overwrite_the_previous_authentication_parameters_if_the_access_token_has_expired(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage,
        AuthenticationParameters $previousParameters
    ) {
        $authenticatedAt = new DateTimeImmutable('@' . time());
        $newParameters = new AuthenticationParameters([
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'xyz987',
            'expires_in' => 3600,
        ]);
        $expiry = $authenticatedAt->add(new DateInterval('PT3600S'));

        $storage->fetch()->willReturn($previousParameters);
        $storage->save($newParameters)->shouldBeCalled();
        $previousParameters->hasAccessTokenExpired()->willReturn(true);
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"xyz987","expires_in":3600}');

        $result = $this->authenticate();

        $result->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $result->getExpiry()->shouldBeLike($expiry);
        $result->getAccessToken()->shouldReturn('xyz987');
        $result->getExpiresIn()->shouldReturn(3600);
    }

    function it_knows_to_make_a_new_call_to_authenticate_and_overwrite_the_previous_authentication_parameters_if_an_exception_is_thrown_when_fetching_the_previous_authentication_parameters(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage,
        LoggerInterface $logger
    ) {
        $exception = new InvalidArgumentException('Access token cannot be empty');
        $authenticatedAt = new DateTimeImmutable('@' . time());
        $newParameters = new AuthenticationParameters([
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'xyz987',
            'expires_in' => 3600,
        ]);
        $expiry = $authenticatedAt->add(new DateInterval('PT3600S'));

        $storage->fetch()->willThrow($exception);
        $storage->save($newParameters)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"xyz987","expires_in":3600}');
        $logger->warning('Fetching Zalando authentication parameters error: Access token cannot be empty')->shouldBeCalled();

        $this->beConstructedWith($client, $storage, $logger);

        $result = $this->authenticate();

        $result->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $result->getExpiry()->shouldBeLike($expiry);
        $result->getAccessToken()->shouldReturn('xyz987');
        $result->getExpiresIn()->shouldReturn(3600);
    }

    function it_throws_an_exception_when_fetching_the_previous_authentication_parameters_throws_an_exception(
        Client $client,
        AuthenticationStorage $storage,
        LoggerInterface $logger
    ) {
        $previousException = new InvalidArgumentException('Access token cannot be empty');
        $exception = new ZalandoException('Fetching Zalando authentication parameters error: Access token cannot be empty');

        $storage->fetch()->willThrow($previousException);
        $storage->save(Argument::any())->shouldNotBeCalled();
        $client->authenticate()->shouldNotBeCalled();
        $logger->warning(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow($exception)->duringAuthenticate();
    }

    function it_throws_an_exception_if_the_call_to_authenticate_fails(Client $client, AuthenticationStorage $storage)
    {
        $clientException = new Exception('Invalid credentials');
        $exception = new ZalandoException('Zalando authentication error: Invalid credentials', 0, $clientException);

        $storage->fetch()->willReturn(null);
        $storage->save(Argument::any())->shouldNotBeCalled();
        $client->authenticate()->willThrow($clientException);

        $this->shouldThrow($exception)->duringAuthenticate();
    }
}
