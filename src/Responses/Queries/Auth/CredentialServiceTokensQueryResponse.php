<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Auth;

use Zonos\ZonosSdk\Data\Auth\CredentialServiceToken;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CredentialServiceTokensQueryResponse extends ZonosResponse
{
  public function resolve(): ?array
  {
    $credentialServiceTokensData = $this->json('data.credentialServiceTokens');
    if ($credentialServiceTokensData === null) {
      return null;
    }
    return array_map(
      fn(array $credentialServiceTokenData) => CredentialServiceToken::fromArray($credentialServiceTokenData),
      $credentialServiceTokensData
    );
  }
}
