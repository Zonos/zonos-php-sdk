<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Logger;

use Saloon\Http\Connector;

/**
 * Connector class for interacting with the Zonos Logging API
 *
 * Handles authentication, base configuration, and provides access to
 * logging via http requests.
 */
class DataDogLoggerConnector extends Connector
{
  /**
   * Create a new DataDogLogger connector instance
   *
   * @param string $credentialToken Authentication token for API access
   * @param string $baseUrl Base URL for API endpoints
   * @param array $clientHeaders Client headers
   */
  public function __construct(
    protected string $credentialToken,
    protected string $baseUrl,
    protected array $clientHeaders,
  ) {
  }

  /**
   * Resolve the base URL
   *
   * @return string
   */
  public function resolveBaseUrl(): string
  {
    return $this->baseUrl;
  }

  /**
   * Get the default headers for API requests
   *
   * @return array<string, string> Array of default headers
   */
  public function defaultHeaders(): array
  {
    return array_merge([
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'service-token' => $this->credentialToken
    ], $this->clientHeaders);
  }
}