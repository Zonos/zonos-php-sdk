<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class OrderQueryResponse extends ZonosResponse
{
  public function resolve(): Order
  {
    return Order::fromArray($this->json('data.order'));
  }
}