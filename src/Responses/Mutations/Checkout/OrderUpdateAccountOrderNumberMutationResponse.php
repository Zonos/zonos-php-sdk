<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Mutations\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class OrderUpdateAccountOrderNumberMutationResponse extends ZonosResponse
{
  public function resolve(): Order
  {
    return Order::fromArray($this->json('data.orderUpdateAccountOrderNumber'));
  }
}