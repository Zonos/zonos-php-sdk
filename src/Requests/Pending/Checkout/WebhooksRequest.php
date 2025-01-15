<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Webhook;
use Zonos\ZonosSdk\Data\Misc\Paginated;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Checkout\WebhooksQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class WebhooksRequest extends PendingZonosRequest
{
  public function __construct(ZonosConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('query', 'webhooks', $args));
  }

  /**
   * @return Paginated<Webhook>|null
   */
  public function get(string ...$fields): ?Paginated
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): WebhooksQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));

    $response = $this->connector->send(new ZonosRequest(WebhooksQueryResponse::class, (string)$query))->throw();
    assert($response instanceof WebhooksQueryResponse);

    return $response;
  }
}