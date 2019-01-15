<?php

namespace spec\Inviqa\Zalando\Api\ArticlePrice;

use Inviqa\Zalando\Api\Article\ArticleReference;
use Inviqa\Zalando\Api\ArticlePrice\ArticlePrice;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticlePrice
 */
class ArticlePriceSpec extends ObjectBehavior
{
    function let(ArticleReference $articleReference)
    {
        $this->beConstructedWith($articleReference, 34.99, 'EUR', '2');
    }

    function it_has_an_article_reference(ArticleReference $articleReference)
    {
        $this->getArticleReference()->shouldReturn($articleReference);
    }

    function it_has_a_regular_price()
    {
        $this->getRegularPrice()->shouldReturn(34.99);
    }

    function it_has_a_currency()
    {
        $this->getCurrency()->shouldReturn('EUR');
    }

    function it_has_a_vat_code()
    {
        $this->getVatCode()->shouldReturn('2');
    }
}
