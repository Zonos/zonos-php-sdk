<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Auth;

use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Data\Auth\CredentialServiceToken;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Auth\GetCredentialServiceTokenQueryResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class GetCredentialServiceTokenRequest extends PendingAuthRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'id',
  ];

  public function __construct(AuthConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('query', 'getCredentialServiceToken', $args));
  }

  public function get(string ...$fields): CredentialServiceToken
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): GetCredentialServiceTokenQueryResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));
    $response = $this->connector->send(new ZonosRequest(GetCredentialServiceTokenQueryResponse::class, (string)$query))->throw();
    assert($response instanceof GetCredentialServiceTokenQueryResponse);

    return $response;
  }
}