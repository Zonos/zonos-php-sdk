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
    $tokenType = 'PUBLIC_TOKEN';
    $organization = $this->getOrganization($credentialId);
    if ($organization == null) {
      throw new InvalidArgumentException("Failed to retrieve organization");
    }
    $credentialToken = $this->getLatestredential($testMode, $organization, $storeId, $tokenType);

    if ($credentialToken == null) {
      throw new InvalidArgumentException("Failed to retrieve service token");
    }
    if ($credentialToken->credential?->type === $tokenType) {
      return $credentialToken->credential->id;
    }

    return $this->createCredential($organization, $tokenType);
  }

  /**
   * Get or create a public key for a given private key
   *
   * @param string $credentialId The credential id
   * @param int $storeId The store id
   * @return string The private key (credential ID)
   * @throws InvalidArgumentException When credential creation or retrival fails
   */
  public function getPrivateKey(string $credentialId, int $storeId, bool $testMode = false): ?string
  {
    $tokenType = 'PRIVATE_TOKEN';
    $organization = $this->getOrganization($credentialId);
    error_log('$organization');
    error_log($organization);
    if ($organization == null) {
      throw new InvalidArgumentException("Failed to retrieve organization");
    }
    $credentialToken = $this->getLatestCredential($testMode, $organization, $storeId, $tokenType);

    if ($credentialToken == null) {
      throw new InvalidArgumentException("Failed to retrieve service token");
    }
    if ($credentialToken->credential?->type === $tokenType) {
      return $credentialToken->credential->id;
    }

    return $this->createCredential($organization, $tokenType);
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
   * @param string $type Public or Private key
   * @return CredentialServiceToken The public credential
   */
  private function getLatestCredential(bool $testMode, string $organization, int $storeId, string $type): ?CredentialServiceToken
  {
    $filter = CredentialServiceTokenQueryFilter::fromArray(
      [
        'mode' => $testMode ? 'TEST' : 'LIVE',
        'organizationId' => $organization,
        'storeId' => $storeId,
        'type' => $type,
      ]
    );
    $credentialServiceTokens = $this->authConnector->credentialServiceTokens($filter)->get(
      'credential.id',
      'credential.type',
    );

    error_log('$credentialServiceTokens');
    error_log($credentialServiceTokens);
    return $credentialServiceTokens && count($credentialServiceTokens) > 0 ? end($credentialServiceTokens) : null;
  }

  /**
   * Create a new credential
   *
   * @param string $organization The organization to create credential for
   * @param bool $testMode Whether to create a test or live credential
   * @param string $type Public or Private key
   * @return string The created credential ID
   * @throws InvalidArgumentException When credential creation fails
   */
  private function createCredential(string $organization, string $type, bool $testMode = false): string
  {
    $input = CredentialCreateInput::fromArray(
      [
        'name' => $tokenType.' Credential for PHP SDK',
        'mode' => $testMode ? 'TEST' : 'LIVE',
        'type' => $tokenType,
        'organization' => $organization,
      ]
    );

    $newCredential = $this->authConnector->credentialCreate($input)->get('id');
    return $newCredential->id;
  }
}