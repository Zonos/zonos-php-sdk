<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Mutations\Auth;

use Zonos\ZonosSdk\Data\Auth\Credential;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class CredentialCreateMutationResponse extends ZonosResponse
{
  public function resolve(): ?Credential
  {
    $credentialData = $this->json('data.credentialCreate');
    if ($credentialData === null) {
      return null;
    }

    return Credential::fromArray($credentialData);
  }
}