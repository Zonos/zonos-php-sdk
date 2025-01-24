<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Mutations\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Cart;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CartCreateMutationResponse extends ZonosResponse
{
  public function resolve(): ?Cart
  {
    $cartData = $this->json('data.cartCreate');
    if ($cartData === null) {
      return null;
    }
    return Cart::fromArray($cartData);
  }
}
