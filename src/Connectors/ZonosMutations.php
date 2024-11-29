<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Requests\Inputs\CheckoutSettingUpdateInput;
use Zonos\ZonosSdk\Requests\Pending\CheckoutSettingsUpdateRequest;

/**
 * Trait for mutations available in the Zonos SDK
 */
trait ZonosMutations
{
  /**
   * Update checkout settings
   *
   * @param CheckoutSettingUpdateInput $input The input data for the mutation
   * @return CheckoutSettingsUpdateRequest
   */
  public function checkoutSettingsUpdate(CheckoutSettingUpdateInput $input): CheckoutSettingsUpdateRequest
  {
    return new CheckoutSettingsUpdateRequest($this, ['input' => $input]);
  }
}
