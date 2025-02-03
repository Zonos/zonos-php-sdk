<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Cart;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Mutations\Checkout\CartCreateMutationResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class CartCreateRequest extends PendingZonosRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'id',
  ];

  public function __construct(ZonosConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('mutation', 'cartCreate', $args));
  }

  public function get(string ...$fields): ?Cart
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): CartCreateMutationResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $response = $this->connector->send(new ZonosRequest(CartCreateMutationResponse::class, (string)$query))->throw();
    assert($response instanceof CartCreateMutationResponse);

    return $response;
  }
}
