<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Auth;

use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialServiceTokenQueryFilter;
use Zonos\ZonosSdk\Requests\Inputs\Auth\GetCredentialServiceTokenInput;
use Zonos\ZonosSdk\Requests\Pending\Auth\CredentialRequest;
use Zonos\ZonosSdk\Requests\Pending\Auth\CredentialServiceTokensRequest;
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

  /**
   * Get a given credential token info
   *
   * @param array $args An associative array of arguments required to create the credential request.
   * @return CredentialRequest Returns an instance of the CredentialRequest class.
   */
  public function credential(array $args): CredentialRequest
  {
    return new CredentialRequest($this, $args);
  }

  /**
   * Get a list of credential service tokens
   *
   * @param CredentialServiceTokenQueryFilter $filter The filter for the credential service tokens
   * @return CredentialServiceTokensRequest Returns an instance of the CredentialServiceTokensRequest class.
   */
  public function credentialServiceTokens(CredentialServiceTokenQueryFilter $filter): CredentialServiceTokensRequest
  {
    return new CredentialServiceTokensRequest($this, ['filter' => $filter]);
  }
}
