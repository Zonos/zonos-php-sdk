<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class OrderQueryResponse extends ZonosResponse
{
  public function resolve(): ?Order
  {
    $orderData = $this->json('data.order');
    if ($orderData === null) {
      return null;
    }
    return Order::fromArray($orderData);
  }
}