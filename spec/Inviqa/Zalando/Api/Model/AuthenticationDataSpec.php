<?php

namespace spec\Inviqa\Zalando\Api\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use Inviqa\Zalando\Api\Model\AuthenticationData;
use PhpSpec\ObjectBehavior;

/**
 * @mixin AuthenticationData
 */
class AuthenticationDataSpec extends ObjectBehavior
{
    function let()
    {
        $authenticationData = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:27'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $this->beConstructedWith($authenticationData);
    }

    function it_gets_authenticated_at()
    {
        $authenticatedAt = new DateTimeImmutable('2019-01-07 11:48:27');

        $this->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
    }

    function it_gets_access_token()
    {
        $this->getAccessToken()->shouldReturn('abc123');
    }

    function it_gets_expires_in_seconds()
    {
        $this->getExpiresIn()->shouldReturn(7200);
    }

    function it_gets_date_and_time_of_access_token_expiration()
    {
        $expiry = new DateTimeImmutable('2019-01-07 13:48:27');

        $this->getExpiry()->shouldBeLike($expiry);
    }

    function it_knows_if_the_access_token_has_expired()
    {
        $authenticationData = [
            'authenticated_at' => new DateTimeImmutable('60 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($authenticationData);

        $this->hasAccessTokenExpired()->shouldReturn(true);
    }

    function it_knows_if_the_access_token_will_expire_soon()
    {
        $authenticationData = [
            'authenticated_at' => new DateTimeImmutable('50 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($authenticationData);

        $this->hasAccessTokenExpired()->shouldReturn(true);
    }

    function it_knows_if_the_access_token_has_not_expired()
    {
        $authenticationData = [
            'authenticated_at' => new DateTimeImmutable('49 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($authenticationData);

        $this->hasAccessTokenExpired()->shouldReturn(false);
    }

    function it_throws_an_exception_if_authenticated_at_is_not_a_date_time_immutable_instance()
    {
        $exception = new InvalidArgumentException('authenticated_at parameter is not a ' . DateTimeImmutable::class);
        $authenticationData = [
            'authenticated_at' => '2019-01-07 11:48:27',
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $this->beConstructedWith($authenticationData);

        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_throws_an_exception_if_access_token_is_empty()
    {
        $exception = new InvalidArgumentException('Access token cannot be empty');
        $authenticationData = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:27'),
            'access_token' => '',
            'expires_in' => 7200,
        ];
        $this->beConstructedWith($authenticationData);

        $this->shouldThrow($exception)->duringInstantiation();
    }
}
