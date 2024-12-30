<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\CheckoutSettings;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CheckoutSettingsMutationResponse extends ZonosResponse
{
  public function resolve(): ?CheckoutSettings
  {
    $checkoutSettingsData = $this->json('data.checkoutSettingsUpdate');
    if ($checkoutSettingsData === null) {
      return null;
    }
    return CheckoutSettings::fromArray($checkoutSettingsData);
  }
}