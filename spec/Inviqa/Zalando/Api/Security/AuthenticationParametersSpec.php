<?php

namespace spec\Inviqa\Zalando\Api\Security;

use DateTimeImmutable;
use InvalidArgumentException;
use Inviqa\Zalando\Api\Security\AuthenticationParameters;
use PhpSpec\ObjectBehavior;

/**
 * @mixin AuthenticationParameters
 */
class AuthenticationParametersSpec extends ObjectBehavior
{
    /**
     * @var array
     */
    private $parameters;

    function let()
    {
        $this->parameters = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:27'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $this->beConstructedWith($this->parameters);
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
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('60 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($parameters);

        $this->hasAccessTokenExpired()->shouldReturn(true);
    }

    function it_knows_if_the_access_token_will_expire_soon()
    {
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('50 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($parameters);

        $this->hasAccessTokenExpired()->shouldReturn(true);
    }

    function it_knows_if_the_access_token_has_not_expired()
    {
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('49 seconds ago'),
            'access_token' => 'abc123',
            'expires_in' => 60,
        ];
        $this->beConstructedWith($parameters);

        $this->hasAccessTokenExpired()->shouldReturn(false);
    }

    function it_throws_an_exception_if_authenticated_at_is_not_a_date_time_immutable_instance()
    {
        $exception = new InvalidArgumentException('authenticated_at parameter is not a ' . DateTimeImmutable::class);
        $parameters = ['authenticated_at' => '2019-01-07 11:48:27'];
        $this->beConstructedWith($parameters);

        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_throws_an_exception_if_access_token_is_empty()
    {
        $exception = new InvalidArgumentException('Access token cannot be empty');
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:27'),
            'access_token' => '',
        ];
        $this->beConstructedWith($parameters);

        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_can_be_converted_to_an_array()
    {
        $this->toArray()->shouldReturn($this->parameters);
    }

    function it_knows_it_is_equal_to_another_instance()
    {
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:27'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $another = new AuthenticationParameters($parameters);

        $this->equals($another)->shouldReturn(true);
    }

    function it_knows_it_is_not_equal_to_another_instance()
    {
        $parameters = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 11:48:28'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $another = new AuthenticationParameters($parameters);

        $this->equals($another)->shouldReturn(false);
    }
}
