<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries;

use Zonos\ZonosSdk\Data\Order;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class OrderQueryResponse extends ZonosResponse
{
  public function resolve(): Order
  {
    return Order::fromArray($this->json('data.order'));
  }
}