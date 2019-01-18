<?php

namespace spec\Inviqa\Zalando\Api\Merchant;

use InvalidArgumentException;
use Inviqa\Zalando\Api\Merchant\MerchantOperationMetadata;
use PhpSpec\ObjectBehavior;

/**
 * @mixin MerchantOperationMetadata
 */
class MerchantOperationMetadataSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('bf48ba35-149d-4b76-8ac9', MerchantOperationMetadata::FULFILLED_BY_ZALANDO);
    }

    function it_has_a_sales_channel_ID()
    {
        $this->getSalesChannelId()->shouldReturn('bf48ba35-149d-4b76-8ac9');
    }

    function it_can_be_fulfilled_by_the_merchant()
    {
        $this->beConstructedWith('bf48ba35-149d-4b76-8ac9', MerchantOperationMetadata::FULFILLED_BY_MERCHANT);

        $this->getFulfillmentType()->shouldReturn(MerchantOperationMetadata::FULFILLED_BY_MERCHANT);
    }

    function it_can_be_fulfilled_by_zalando()
    {
        $this->getFulfillmentType()->shouldReturn(MerchantOperationMetadata::FULFILLED_BY_ZALANDO);
    }

    function it_throws_an_exception_if_unable_to_assert_valid_fulfillment_type()
    {
        $exception = new InvalidArgumentException('Unknown fulfillment type "fulfilled_by_other"');

        $this->beConstructedWith('bf48ba35-149d-4b76-8ac9', 'fulfilled_by_other');

        $this->shouldThrow($exception)->duringInstantiation();
    }
}
