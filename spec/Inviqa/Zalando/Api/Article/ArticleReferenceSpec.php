<?php

namespace spec\Inviqa\Zalando\Api\Article;

use Inviqa\Zalando\Api\Article\ArticleReference;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticleReference
 */
class ArticleReferenceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('cool-blue-shoes', '9780679762881');
    }

    function it_has_a_merchant_simple_ID()
    {
        $this->getMerchantSimpleId()->shouldReturn('cool-blue-shoes');
    }

    function it_has_an_ean()
    {
        $this->getEan()->shouldReturn('9780679762881');
    }
}
