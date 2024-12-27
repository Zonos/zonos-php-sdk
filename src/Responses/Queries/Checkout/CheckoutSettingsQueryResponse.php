<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\CheckoutSettings;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CheckoutSettingsQueryResponse extends ZonosResponse
{
    public function resolve(): CheckoutSettings
    {
        return CheckoutSettings::fromArray($this->json('data.checkoutSettings'));
    }
}