<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Auth;

use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Data\Auth\Credential;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Mutations\Auth\CredentialCreateMutationResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class CredentialCreateRequest extends PendingAuthRequest
{
  protected const DEFAULT_ATTRIBUTES = [
    'id',
  ];

  public function __construct(AuthConnector $connector, public array $args = [])
  {
    parent::__construct($connector, GqlBuilder::make('mutation', 'credentialCreate', $args));
  }

  public function get(string ...$fields): ?Credential
  {
    return $this->response(...$fields)->resolve();
  }

  public function response(string ...$fields): CredentialCreateMutationResponse
  {
    $query = $this->query->withFields($this->normalizeFields($fields));

    $response = $this->connector->send(new ZonosRequest(CredentialCreateMutationResponse::class, (string)$query))->throw();
    assert($response instanceof CredentialCreateMutationResponse);

    return $response;
  }
}