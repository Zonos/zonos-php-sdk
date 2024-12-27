<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Auth;

use Zonos\ZonosSdk\Data\Auth\CredentialServiceToken;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class GetCredentialServiceTokenQueryResponse extends ZonosResponse
{
  public function resolve(): CredentialServiceToken
  {
    return CredentialServiceToken::fromArray($this->json('data.getCredentialServiceToken'));
  }
}