<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Requests\Inputs\Auth\CredentialCreateInput;
use Zonos\ZonosSdk\Requests\Inputs\Auth\GetCredentialServiceTokenInput;

/**
 * Service for mapping data between different formats and structures
 */
class ZonosAuthService
{
  /**
   * Create a new DataMapperService instance
   *
   * @param ZonosConfig $config Configuration settings
   */
  public function __construct(
    private readonly ZonosConfig $config,
    private readonly AuthConnector $authConnector
  ) {
  }

  public function get_public_key(int $private_key): string
  {
    $input = GetCredentialServiceTokenInput::fromArray(['storeId' => $private_key, 'mode' => 'LIVE']);
    $credentialToken = $this->authConnector->getCredentialServiceToken($input)->get(
      'storeId',
      'credential.organization',
      'credential.id',
      'credential.type',
    );

    if ($credentialToken && $credentialToken->credential && $credentialToken->credential->type === 'PUBLIC_TOKEN') {
      return $credentialToken->credential->id;
    }

    $newCredentialTokenInput = CredentialCreateInput::fromArray([
      'name' => 'Public Credential',
      'mode' => 'LIVE',
      'type' => 'PUBLIC_TOKEN',
      'organization' => $credentialToken->credential->organization,
    ]);
    $newCredentialToken = $this->authConnector->credentialCreate($newCredentialTokenInput)->get('id');

    return $newCredentialToken->id;
  }
}