<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Auth;

use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialCreateInput;
use Zonos\ZonosSdk\Requests\Pending\Auth\CredentialCreateRequest;

/**
 * Trait for mutations available in the Zonos SDK
 */
trait AuthMutations
{
  public function credentialCreate(CredentialCreateInput $input): CredentialCreateRequest
  {
    return new CredentialCreateRequest($this, ['input' => $input]);
  }
}
