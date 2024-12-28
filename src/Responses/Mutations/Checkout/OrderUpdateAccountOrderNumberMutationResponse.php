<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Mutations\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class OrderUpdateAccountOrderNumberMutationResponse extends ZonosResponse
{
  public function resolve(): ?Order
  {
    $orderData = $this->json('data.orderUpdateAccountOrderNumber');
    if ($orderData === null) {
      return null;
    }
    return Order::fromArray($orderData);
  }
}