<?php

namespace spec\Inviqa\Zalando\Api\Response;

use Inviqa\Zalando\Api\Response\ArticlePriceUpdateResponse;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticlePriceUpdateResponse
 */
class ArticlePriceUpdateResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('{"price":34.99}');
    }

    function it_has_the_raw_request()
    {
        $this->getRawRequest()->shouldReturn('{"price":34.99}');
    }
}
