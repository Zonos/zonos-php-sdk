<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Auth;

use Zonos\ZonosSdk\Requests\Inputs\Auth\GetCredentialServiceTokenInput;
use Zonos\ZonosSdk\Requests\Pending\Auth\GetCredentialServiceTokenRequest;

/**
 * Trait for queries available in the Zonos SDK
 */
trait AuthQueries
{
  public function getCredentialServiceToken(GetCredentialServiceTokenInput $input): GetCredentialServiceTokenRequest
  {
    return new GetCredentialServiceTokenRequest($this, ['input' => $input]);
  }
}
