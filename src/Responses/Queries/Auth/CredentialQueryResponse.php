<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Auth;

use Zonos\ZonosSdk\Data\Auth\Credential;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CredentialQueryResponse extends ZonosResponse
{
  public function resolve(): ?Credential
  {
    $credential = $this->json('data.credential');
    if ($credential === null) {
      return null;
    }
    return Credential::fromArray($credential);
  }
}

