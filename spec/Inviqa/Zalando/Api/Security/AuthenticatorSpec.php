<?php

namespace spec\Inviqa\Zalando\Api\Security;

use DateInterval;
use DateTimeImmutable;
use Exception;
use Inviqa\Zalando\Api\Client;
use Inviqa\Zalando\Api\Exception\ZalandoException;
use Inviqa\Zalando\Api\Response\ClientResponse;
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

    function it_authenticates_then_stores_the_response_and_returns_authenticated_data(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage
    ) {
        $authenticatedAt = new DateTimeImmutable('2019-01-07 15:47:53');
        $expiry = new DateTimeImmutable('2019-01-07 17:47:53');
        $data = [
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];

        $storage->fetch()->willReturn([]);
        $storage->save($data)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"abc123","expires_in":7200}');

        $authenticationData = $this->authenticate();

        $authenticationData->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $authenticationData->getExpiry()->shouldBeLike($expiry);
        $authenticationData->getAccessToken()->shouldReturn('abc123');
        $authenticationData->getExpiresIn()->shouldReturn(7200);
    }

    function it_knows_not_to_make_a_new_call_to_authenticate_and_to_use_the_previous_authentication_configuration(
        Client $client,
        AuthenticationStorage $storage
    ) {
        $authenticatedAt = new DateTimeImmutable('@' . (time() - 3600));
        $data = [
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $expiry = $authenticatedAt->add(new DateInterval('PT7200S'));

        $storage->fetch()->willReturn($data);
        $storage->save($data)->shouldNotBeCalled();
        $client->authenticate()->shouldNotBeCalled();

        $authenticationData = $this->authenticate();

        $authenticationData->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $authenticationData->getExpiry()->shouldBeLike($expiry);
        $authenticationData->getAccessToken()->shouldReturn('abc123');
        $authenticationData->getExpiresIn()->shouldReturn(7200);
    }

    function it_knows_to_make_a_new_call_to_authenticate_when_the_previous_authentication_configuration_is_empty(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage
    ) {
        $authenticatedAt = new DateTimeImmutable('2019-01-07 15:47:53');
        $data = [
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $expiry = new DateTimeImmutable('2019-01-07 17:47:53');

        $storage->fetch()->willReturn([]);
        $storage->save($data)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"abc123","expires_in":7200}');

        $authenticationData = $this->authenticate();

        $authenticationData->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $authenticationData->getExpiry()->shouldBeLike($expiry);
        $authenticationData->getAccessToken()->shouldReturn('abc123');
        $authenticationData->getExpiresIn()->shouldReturn(7200);
    }

    function it_knows_to_make_a_new_call_to_authenticate_and_overwrite_the_previous_authentication_configuration_if_the_access_token_has_expired(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage
    ) {
        $previousAuthenticatedAt = new DateTimeImmutable('2 hour ago');
        $previousData = [
            'authenticated_at' => $previousAuthenticatedAt,
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $authenticatedAt = new DateTimeImmutable('@' . time());
        $newData = [
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'xyz987',
            'expires_in' => 3600,
        ];
        $expiry = $authenticatedAt->add(new DateInterval('PT3600S'));

        $storage->fetch()->willReturn($previousData);
        $storage->save($newData)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"xyz987","expires_in":3600}');

        $authenticationData = $this->authenticate();

        $authenticationData->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $authenticationData->getExpiry()->shouldBeLike($expiry);
        $authenticationData->getAccessToken()->shouldReturn('xyz987');
        $authenticationData->getExpiresIn()->shouldReturn(3600);
    }

    function it_knows_to_make_a_new_call_to_authenticate_and_overwrite_the_previous_authentication_configuration_if_an_exception_is_thrown_due_to_an_error_in_the_previous_authentication_configuration(
        Client $client,
        ClientResponse $response,
        AuthenticationStorage $storage,
        LoggerInterface $logger
    ) {
        $previousAuthenticatedAt = new DateTimeImmutable('1 hour ago');
        $previousData = [
            'authenticated_at' => $previousAuthenticatedAt,
            'access_token' => '',
            'expires_in' => 7200,
        ];
        $authenticatedAt = new DateTimeImmutable('@' . time());
        $newData = [
            'authenticated_at' => $authenticatedAt,
            'access_token' => 'xyz987',
            'expires_in' => 3600,
        ];
        $expiry = $authenticatedAt->add(new DateInterval('PT3600S'));

        $storage->fetch()->willReturn($previousData);
        $storage->save($newData)->shouldBeCalled();
        $client->authenticate()->willReturn($response);
        $response->getDate()->willReturn($authenticatedAt);
        $response->getContent()->willReturn('{"access_token":"xyz987","expires_in":3600}');
        $logger->warning('Access token cannot be empty')->shouldBeCalled();

        $this->beConstructedWith($client, $storage, $logger);

        $authenticationData = $this->authenticate();

        $authenticationData->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $authenticationData->getExpiry()->shouldBeLike($expiry);
        $authenticationData->getAccessToken()->shouldReturn('xyz987');
        $authenticationData->getExpiresIn()->shouldReturn(3600);
    }

    function it_throws_an_exception_if_the_previous_authentication_configuration_has_an_error(
        Client $client,
        AuthenticationStorage $storage,
        LoggerInterface $logger
    ) {
        $previousAuthenticatedAt = new DateTimeImmutable('1 hour ago');
        $previousData = [
            'authenticated_at' => $previousAuthenticatedAt,
            'access_token' => '',
            'expires_in' => 7200,
        ];
        $exception = new ZalandoException('Access token cannot be empty');

        $storage->fetch()->willReturn($previousData);
        $storage->save(Argument::any())->shouldNotBeCalled();
        $client->authenticate()->shouldNotBeCalled();
        $logger->warning(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow($exception)->duringAuthenticate();
    }

    function it_throws_an_exception_if_the_call_to_authenticate_fails(Client $client, AuthenticationStorage $storage)
    {
        $clientException = new Exception('Invalid credentials');
        $exception = new ZalandoException('Zalando authentication error: Invalid credentials', 0, $clientException);

        $storage->fetch()->willReturn([]);
        $storage->save(Argument::any())->shouldNotBeCalled();
        $client->authenticate()->willThrow($clientException);

        $this->shouldThrow($exception)->duringAuthenticate();
    }
}
