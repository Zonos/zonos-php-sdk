<?php declare(strict_types=1);
/**
 * Main connector class for interacting with the Zonos API
 * Handles authentication and base configuration
 */

namespace Zonos\ZonosSdk\Connectors;

use Saloon\Http\Connector;

/**
 * Main connector class for interacting with the Zonos API
 * Handles authentication and base configuration
 */
class ZonosConnector extends Connector
{
  use ZonosQueries;
  use ZonosMutations;

  /**
   * Create a new Zonos connector instance
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
      'credentialToken' => $this->credential_token
    ];
  }
}