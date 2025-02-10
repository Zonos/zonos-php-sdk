<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Checkout;

use Zonos\ZonosSdk\Requests\Inputs\Checkout\CartCreateInput;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\CheckoutSettingUpdateInput;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\OrderUpdateAccountOrderNumberInput;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\WebhookCreateInput;
use Zonos\ZonosSdk\Requests\Pending\Checkout\CartCreateRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\CheckoutSettingsUpdateRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\OrderUpdateAccountOrderNumberRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\WebhookCreateRequest;

/**
 * Trait for mutations available in the Zonos SDK
 */
trait ZonosMutations
{
  /**
   * Create a cart
   *
   * @param CartCreateInput $input The input data for creating a cart
   * @return CartCreateRequest A pending request for cart creation
   */
  public function cartCreate(CartCreateInput $input): CartCreateRequest
  {
    return new CartCreateRequest($this, ['input' => $input]);
  }

  /**
   * Update checkout settings
   *
   * @param CheckoutSettingUpdateInput $input The input data for updating checkout settings
   * @return CheckoutSettingsUpdateRequest A pending request for settings update
   */
  public function checkoutSettingsUpdate(CheckoutSettingUpdateInput $input): CheckoutSettingsUpdateRequest
  {
    return new CheckoutSettingsUpdateRequest($this, ['input' => $input]);
  }

  /**
   * Update order account number
   *
   * @param OrderUpdateAccountOrderNumberInput $input The input data for updating order number
   * @return OrderUpdateAccountOrderNumberRequest A pending request for order update
   */
  public function orderUpdateAccountOrderNumber(OrderUpdateAccountOrderNumberInput $input, bool $withRetry = false): OrderUpdateAccountOrderNumberRequest
  {
    return new OrderUpdateAccountOrderNumberRequest($this, ['input' => $input], $withRetry);
  }

  /**
   * Create a webhook
   *
   * @param WebhookCreateInput $input The input data for creating a webhook
   * @return WebhookCreateRequest A pending request for webhook creation
   */
  public function webhookCreate(WebhookCreateInput $input): WebhookCreateRequest
  {
    return new WebhookCreateRequest($this, ['input' => $input]);
  }
}
