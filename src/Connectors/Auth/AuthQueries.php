<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Auth;

use Zonos\ZonosSdk\Requests\Inputs\Auth\GetCredentialServiceTokenInput;
use Zonos\ZonosSdk\Requests\Pending\Auth\GetCredentialServiceTokenRequest;

/**
 * Trait for queries available in the Zonos SDK
 */
trait AuthQueries
{
  /**
   * Get a credential service token
   *
   * @param GetCredentialServiceTokenInput $input The input data for retrieving a service token
   * @return GetCredentialServiceTokenRequest A pending request for service token retrieval
   */
  public function getCredentialServiceToken(GetCredentialServiceTokenInput $input): GetCredentialServiceTokenRequest
  {
    return new GetCredentialServiceTokenRequest($this, ['input' => $input]);
  }
}
