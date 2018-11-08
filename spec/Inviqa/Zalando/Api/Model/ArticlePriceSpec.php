<?php

namespace spec\Inviqa\Zalando\Api\Model;

use Inviqa\Zalando\Api\Model\ArticlePrice;
use PhpSpec\ObjectBehavior;

/**
 * @mixin ArticlePrice
 */
class ArticlePriceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(34.99, 'cool-blue-shoes', '9780679762881', 'bf48ba35-149d-4b76-8ac9-d08d126b517f');
    }

    function it_has_a_regular_price()
    {
        $this->getRegularPrice()->shouldReturn(34.99);
    }

    function it_has_a_currency()
    {
        $this->getCurrency()->shouldReturn('EUR');
    }

    function it_throws_an_exception_if_unable_to_assert_valid_currency()
    {
        $exception = new \InvalidArgumentException('Unknown currency "ABC"');

        $this->beConstructedWith(34.99, 'cool-blue-shoes', '9780679762881', 'bf48ba35-149d-4b76-8ac9', 'ABC');

        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_has_a_vat_code()
    {
        $this->getVatCode()->shouldReturn('2');
    }

    function it_throws_an_exception_if_unable_to_assert_valid_VAT_code()
    {
        $exception = new \InvalidArgumentException('Unknown VAT code "3"');

        $this->beConstructedWith(34.99, 'blue-shoes', '9780679762', 'bf48ba35-149d', ArticlePrice::CURRENCY_EUR, '3');

        $this->shouldThrow($exception)->duringInstantiation();
    }

    function it_has_a_merchant_simple_ID()
    {
        $this->getMerchantSimpleId()->shouldReturn('cool-blue-shoes');
    }

    function it_has_an_ean()
    {
        $this->getEan()->shouldReturn('9780679762881');
    }

    function it_has_a_sales_channel_ID()
    {
        $this->getSalesChannelId()->shouldReturn('bf48ba35-149d-4b76-8ac9-d08d126b517f');
    }

    function it_can_be_fulfilled_by_the_merchant()
    {
        $this->beConstructedWith(
            34.99,
            'blue-shoes',
            '9780679762',
            'bf48ba35-149d',
            ArticlePrice::CURRENCY_EUR,
            ArticlePrice::VAT_CODE,
            ArticlePrice::FULFILLED_BY_MERCHANT
        );

        $this->getFulfillmentType()->shouldReturn('fulfilled_by_merchant');
    }

    function it_can_be_fulfilled_by_zalando()
    {
        $this->getFulfillmentType()->shouldReturn('fulfilled_by_zalando');
    }

    function it_throws_an_exception_if_unable_to_assert_valid_fulfillment_type()
    {
        $exception = new \InvalidArgumentException('Unknown fulfillment type "fulfilled_by_other"');

        $this->beConstructedWith(
            34.99,
            'blue-shoes',
            '9780679762',
            'bf48ba35-149d',
            ArticlePrice::CURRENCY_EUR,
            ArticlePrice::VAT_CODE,
            'fulfilled_by_other'
        );

        $this->shouldThrow($exception)->duringInstantiation();
    }
}
