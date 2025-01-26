<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\PluginCredential;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Checkout\PluginCredentialQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class PluginCredentialRequest extends PendingZonosRequest
{
  public function __construct(ZonosConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('query', 'pluginCredential', $args));
  }

  /**
   * @return credential token for the plugin usage
   */
  public function get(string ...$fields): ?PluginCredential
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): PluginCredentialQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $response = $this->connector->send(new ZonosRequest(PluginCredentialQueryResponse::class, (string)$query))->throw();

    assert($response instanceof PluginCredentialQueryResponse);

    return $response;
  }
}