<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Mutations\Checkout\OrderUpdateAccountOrderNumberMutationResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class OrderUpdateAccountOrderNumberRequest extends PendingZonosRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'id',
  ];

  public function __construct(ZonosConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('mutation', 'orderUpdateAccountOrderNumber', $args));
  }

  public function get(string ...$fields): ?Order
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): OrderUpdateAccountOrderNumberMutationResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));

    $response = $this->connector->send(new ZonosRequest(OrderUpdateAccountOrderNumberMutationResponse::class, (string)$query))->throw();
    assert($response instanceof OrderUpdateAccountOrderNumberMutationResponse);

    return $response;
  }
}