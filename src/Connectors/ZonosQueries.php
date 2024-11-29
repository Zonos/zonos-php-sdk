<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Requests\Pending\CheckoutSettingsRequest;

/**
 * Trait for queries available in the Zonos SDK
 */
trait ZonosQueries
{
  /**
   * Get checkout settings
   *
   * @return CheckoutSettingsRequest
   */
  public function checkoutSettings(): CheckoutSettingsRequest
  {
    return new CheckoutSettingsRequest($this);
  }
}
