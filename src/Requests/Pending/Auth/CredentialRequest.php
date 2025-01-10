<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Auth;

use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Data\Auth\Credential;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Auth\CredentialQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class CredentialRequest extends PendingAuthRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'organization'
  ];

  public function __construct(AuthConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('query', 'credential', $args));
  }

  public function get(string ...$fields): ?Credential
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): CredentialQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $response = $this->connector->send(new ZonosRequest(CredentialQueryResponse::class, (string)$query))->throw();
    assert($response instanceof CredentialQueryResponse);

    return $response;
  }
}