<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Checkout;

use Zonos\ZonosSdk\Requests\Pending\Checkout\CheckoutSettingsRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\OrderRequest;

/**
 * Trait for queries available in the Zonos SDK
 */
trait ZonosQueries
{
  /**
   * Get checkout settings
   *
   * @return CheckoutSettingsRequest A pending request for checkout settings
   */
  public function checkoutSettings(): CheckoutSettingsRequest
  {
    return new CheckoutSettingsRequest($this);
  }

  /**
   * Get order information
   *
   * @param array<string, mixed> $args The arguments for the order query
   * @return OrderRequest A pending request for order information
   */
  public function order(array $args): OrderRequest
  {
    return new OrderRequest($this, $args);
  }
}
