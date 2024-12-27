<?php declare(strict_types=1);
/**
 * Main connector class for interacting with the Auth API
 * Handles authentication and base configuration
 */

namespace Zonos\ZonosSdk\Connectors\Auth;

use Saloon\Http\Connector;

/**
 * Main connector class for interacting with the Auth API
 * Handles authentication and base configuration
 */
class AuthConnector extends Connector
{
  use AuthQueries;
  use AuthMutations;

  /**
   * Create a new Auth connector instance
   *
   * @param string $credential_token Authentication token for API access
   * @param string $base_url Base URL for API endpoints
   */
  public function __construct(
    protected string $credential_token,
    protected string $base_url,
  ) {
  }

  /**
   * Resolve the base URL
   *
   * @return string
   */
  public function resolveBaseUrl(): string
  {
    return $this->base_url;
  }

  /**
   * Default headers for the connector
   *
   * @return array
   */
  protected function defaultHeaders(): array
  {
    return [
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'credentialToken' => $this->credential_token,
      'senderCredential' => 'credential_live_2d26de91-f0ca-4a67-9642-be2db2ded1f6'
    ];
  }
}