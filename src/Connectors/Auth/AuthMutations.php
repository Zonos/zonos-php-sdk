<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Auth;

use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialCreateInput;
use Zonos\ZonosSdk\Requests\Pending\Auth\CredentialCreateRequest;

/**
 * Trait for mutations available in the Zonos SDK
 */
trait AuthMutations
{
  /**
   * Create a new credential
   *
   * @param CredentialCreateInput $input The input data for creating a credential
   * @return CredentialCreateRequest A pending request for credential creation
   */
  public function credentialCreate(CredentialCreateInput $input): CredentialCreateRequest
  {
    return new CredentialCreateRequest($this, ['input' => $input]);
  }
}
