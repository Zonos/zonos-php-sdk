<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Requests\Inputs\CheckoutSettingUpdateInput;
use Zonos\ZonosSdk\Requests\Pending\CheckoutSettingsUpdateRequest;

trait ZonosMutations
{
    public function checkoutSettingsUpdate(CheckoutSettingUpdateInput $input): CheckoutSettingsUpdateRequest
    {
        return new CheckoutSettingsUpdateRequest($this, ['input' => $input]);
    }
}