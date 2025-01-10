<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Auth;

use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Auth\CredentialServiceTokensQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class CredentialServiceTokensRequest extends PendingAuthRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'credential.id',
  ];

  public function __construct(AuthConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('query', 'credentialServiceTokens', $args));
  }

  public function get(string ...$fields): ?array
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): CredentialServiceTokensQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $response = $this->connector->send(new ZonosRequest(CredentialServiceTokensQueryResponse::class, (string)$query))->throw();
    assert($response instanceof CredentialServiceTokensQueryResponse);

    return $response;
  }
}
