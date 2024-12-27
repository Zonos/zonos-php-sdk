<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Checkout;

use Zonos\ZonosSdk\Requests\Inputs\Checkout\CheckoutSettingUpdateInput;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\OrderUpdateAccountOrderNumberInput;
use Zonos\ZonosSdk\Requests\Pending\Checkout\CheckoutSettingsUpdateRequest;
use Zonos\ZonosSdk\Requests\Pending\Checkout\OrderUpdateAccountOrderNumberRequest;

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

  /**
   * Update Order Account number
   *
   * @param OrderUpdateAccountOrderNumberInput $input The input data for the mutation
   * @return OrderUpdateAccountOrderNumberRequest The order updated
   */
  public function orderUpdateAccountOrderNumber(OrderUpdateAccountOrderNumberInput $input): OrderUpdateAccountOrderNumberRequest
  {
    return new OrderUpdateAccountOrderNumberRequest($this, ['input' => $input]);
  }
}
