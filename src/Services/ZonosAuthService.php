<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Data\Auth\CredentialServiceToken;
use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialCreateInput;
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
   * @param ZonosConfig $config Configuration settings for the service
   * @param AuthConnector $authConnector Connector for auth-related API calls
   */
  public function __construct(
    private readonly ZonosConfig   $config,
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
  public function getPublicKey(int $storeId, bool $testMode = false): ?string
  {
    $credentialToken = $this->getExistingCredential($storeId, $testMode);

    if ($credentialToken == null) {
      throw new InvalidArgumentException("Failed to retrieve service token");
    }
    if ($credentialToken?->credential?->type === 'PUBLIC_TOKEN') {
      return $credentialToken->credential->id;
    }

    return $this->createPublicCredential($credentialToken->credential->organization);
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
   * @return string The created credential ID
   * @throws InvalidArgumentException When credential creation fails
   */
  private function createPublicCredential(string $organization): string
  {
    $input = CredentialCreateInput::fromArray(
      [
        'name' => 'Public Credential for PHP SDK',
        'mode' => 'LIVE',
        'type' => 'PUBLIC_TOKEN',
        'organization' => $organization,
      ]
    );

    $newCredential = $this->authConnector->credentialCreate($input)->get('id');
    return $newCredential->id;
  }
}