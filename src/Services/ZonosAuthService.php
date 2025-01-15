<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Data\Auth\CredentialServiceToken;
use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialCreateInput;
use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialServiceTokenQueryFilter;
use Zonos\ZonosSdk\Requests\Inputs\Auth\GetCredentialServiceTokenInput;

/**
 * Service for handling Zonos authentication operations
 *
 * Manages credential creation and retrieval, including public/private key
 * management and token generation.
 */
class ZonosAuthService
{
  /**
   * Create a new ZonosAuthService instance
   *
   * @param AuthConnector $authConnector Connector for auth-related API calls
   */
  public function __construct(
    private readonly AuthConnector $authConnector
  ) {
  }

  /**
   * Get or create a public key for a given private key
   *
   * @param int $storeId The store id
   * @return string The public key (credential ID)
   * @throws InvalidArgumentException When credential creation or retrival fails
   */
  public function getPublicKey(string $credentialId, int $storeId, bool $testMode = false): ?string
  {
    $organization = $this->getOrganization($credentialId);
    if ($organization == null) {
      throw new InvalidArgumentException("Failed to retrieve organization");
    }
    $credentialToken = $this->getLatestPublicCredential($testMode, $organization, $storeId);

    if ($credentialToken == null) {
      throw new InvalidArgumentException("Failed to retrieve service token");
    }
    if ($credentialToken->credential?->type === 'PUBLIC_TOKEN') {
      return $credentialToken->credential->id;
    }

    return $this->createPublicCredential($organization);
  }

  /**
   * Get the organization for a given credential
   *
   * @param string $credentialId The credential ID
   * @return string The organization
   */
  private function getOrganization(string $credentialId): ?string
  {
    $credential = $this->authConnector->credential(['id' => $credentialId])->get('organization');
    return $credential->organization;
  }

  /**
   * Get the latest public credential for a given organization
   *
   * @param bool $testMode Whether to get the test or live credential
   * @param string $organization The organization
   * @param int $storeId The store ID
   * @return CredentialServiceToken The public credential
   */
  private function getLatestPublicCredential(bool $testMode, string $organization, int $storeId): ?CredentialServiceToken
  {
    $filter = CredentialServiceTokenQueryFilter::fromArray(
      [
        'mode' => $testMode ? 'TEST' : 'LIVE',
        'organizationId' => $organization,
        'storeId' => $storeId,
        'type' => 'PUBLIC_TOKEN',
      ]
    );
    $credentialServiceTokens = $this->authConnector->credentialServiceTokens($filter)->get(
      'credential.id',
      'credential.type',
    );

    return count($credentialServiceTokens) > 0 ? end($credentialServiceTokens) : null;
  }

  /**
   * Get existing credential for a store
   *
   * @param int $storeId The store ID to get credential for
   * @return CredentialServiceToken The credential service token
   * @throws InvalidArgumentException When credential retrieval fails
   */
  private function getExistingCredential(int $storeId, bool $testMode): ?CredentialServiceToken
  {
    $input = GetCredentialServiceTokenInput::fromArray(
      [
        'storeId' => $storeId,
        'mode' => $testMode ? 'TEST' : 'LIVE'
      ]
    );

    return $this->authConnector->getCredentialServiceToken($input)->get(
      'storeId',
      'credential.organization',
      'credential.id',
      'credential.type',
    );
  }

  /**
   * Create a new public credential
   *
   * @param string $organization The organization to create credential for
   * @param bool $testMode Whether to create a test or live credential
   * @return string The created credential ID
   * @throws InvalidArgumentException When credential creation fails
   */
  private function createPublicCredential(string $organization, bool $testMode = false): string
  {
    $input = CredentialCreateInput::fromArray(
      [
        'name' => 'Public Credential for PHP SDK',
        'mode' => $testMode ? 'TEST' : 'LIVE',
        'type' => 'PUBLIC_TOKEN',
        'organization' => $organization,
      ]
    );

    $newCredential = $this->authConnector->credentialCreate($input)->get('id');
    return $newCredential->id;
  }
}