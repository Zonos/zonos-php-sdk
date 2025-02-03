<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Cart;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CartQueryResponse extends ZonosResponse
{
  public function resolve(): ?Cart
  {
    $cartData = $this->json('data.cart');
    if ($cartData === null) {
      return null;
    }
    return Cart::fromArray($cartData);
  }
}
