<?php

namespace spec\Inviqa\Zalando\Api\Response;

use DateTimeImmutable;
use Inviqa\Zalando\Api\Response\ClientResponse;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @mixin ClientResponse
 */
class ClientResponseSpec extends ObjectBehavior
{
    function let(ResponseInterface $response, StreamInterface $body)
    {
        $response->getStatusCode()->willReturn(200);
        $response->getHeaderLine('Date')->willReturn('Mon, 07 Jan 2019 15:47:53 GMT');
        $response->getBody()->willReturn($body);
        $body->getContents()->willReturn('{"access_token":"abc123"}');

        $this->beConstructedWith($response);
    }

    function it_knows_it_was_successful()
    {
        $this->isSuccessful()->shouldReturn(true);
    }

    function it_knows_it_was_not_successful(ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(501);

        $this->isSuccessful()->shouldReturn(false);
    }

    function it_gets_content()
    {
        $this->getContent()->shouldReturn('{"access_token":"abc123"}');
    }

    function it_gets_date_from_the_response()
    {
        $this->getDate()->shouldBeLike(new DateTimeImmutable('2019-01-07 15:47:53'));
    }

    function it_gets_current_date_if_not_available_in_the_response(ResponseInterface $response)
    {
        $response->getHeaderLine('Date')->willReturn(null);

        $this->getDate()->shouldReturnAnInstanceOf(DateTimeImmutable::class);
    }
}
