<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Checkout;

use Zonos\ZonosSdk\Requests\Pending\Checkout\CheckoutSettingsRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\OrderRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\WebhooksRequest;

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
  public function order(array $args, bool $withRetry): OrderRequest
  {
    return new OrderRequest($this, $args, $withRetry);
  }

  /**
   * Get webhooks
   *
   * @param array<string, mixed> $args The arguments for the webhooks query
   * @return WebhooksRequest A pending request for webhooks
   */
  public function webhooks(array $args = []): WebhooksRequest
  {
    return new WebhooksRequest($this, $args);
  }
}
