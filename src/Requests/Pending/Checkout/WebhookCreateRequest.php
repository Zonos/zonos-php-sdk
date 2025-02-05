<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Webhook;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Mutations\Checkout\WebhookCreateMutationResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class WebhookCreateRequest extends PendingZonosRequest
{
  public function __construct(ZonosConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('mutation', 'webhookCreate', $args));
  }

  public function get(string ...$fields): ?Webhook
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): WebhookCreateMutationResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));

    $response = $this->connector->send(new ZonosRequest(WebhookCreateMutationResponse::class, (string)$query))->throw();
    assert($response instanceof WebhookCreateMutationResponse);

    return $response;
  }
}
