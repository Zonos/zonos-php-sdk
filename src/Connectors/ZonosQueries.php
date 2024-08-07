<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Requests\Pending\CheckoutSettingsRequest;

trait ZonosQueries
{
    public function checkoutSettings(): CheckoutSettingsRequest
    {
        return new CheckoutSettingsRequest($this);
    }
}