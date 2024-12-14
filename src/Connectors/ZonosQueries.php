<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Requests\Pending\CheckoutSettingsRequest;
use Zonos\ZonosSdk\Requests\Pending\OrderRequest;

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


  public function order($args): OrderRequest
  {
    return new OrderRequest($this, $args);
  }
}
