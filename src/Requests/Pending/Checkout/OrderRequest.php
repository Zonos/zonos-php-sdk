<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Checkout\OrderQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class OrderRequest extends PendingZonosRequest
{
  private bool $withRetry;
  protected const DEFAULT_ATTRIBUTES = [
    'id',
  ];

  public function __construct(ZonosConnector $connector, public array $args = [], bool $withRetry)
  {
    parent::__construct($connector, GqlBuilder::make('query', 'order', $args));
    $this->withRetry = $withRetry;
  }

  public function get(string ...$fields): ?Order
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): OrderQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $request = new ZonosRequest(OrderQueryResponse::class, (string)$query);
    $response = $this->connector->send($request)->throw();

    if (!isset($response) && $this->withRetry) {
      $request->headers()->add('credentialToken', $this->connector->getTestCredentialToken());
      $response = $this->connector->send($request)->throw();
    }

    assert($response instanceof OrderQueryResponse);

    return $response;
  }
}