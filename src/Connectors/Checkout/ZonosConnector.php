<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Checkout;

use Saloon\Http\Connector;

/**
 * Main connector class for interacting with the Zonos Checkout API
 *
 * Handles authentication, base configuration, and provides access to
 * checkout-related queries and mutations through trait implementations.
 */
class ZonosConnector extends Connector
{
  use ZonosQueries;
  use ZonosMutations;

  private string $testCredentialToken = '';
  /**
   * Create a new Zonos connector instance
   *
   * @param string $credentialToken Authentication token for API access
   * @param string $baseUrl Base URL for API endpoints
   * @param array $clientHeaders Client headers
   */
  public function __construct(
    protected string           $credentialToken,
    protected string           $baseUrl,
    protected array            $clientHeaders,
  ) {
  }

  /**
   * Resolve the base URL for API endpoints
   *
   * @return string The complete base URL for the API
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
  protected function defaultHeaders(): array
  {
    if (!$this->credentialToken) {
      error_log('Trying to make request without the credential token set');
    }

    return array_merge([
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'credentialToken' => $this->credentialToken,
    ], $this->clientHeaders);
  }

  public function setTestCredentialToken(string $testToken): void
  {
    $this->testCredentialToken = $testToken;
  }

  public function getTestCredentialToken(): string
  {
    return $this->testCredentialToken ?? '';
  }

  public function getSource(): string
  {
    return $this->clientHeaders['x-client-name'] ?? '';
  }
}