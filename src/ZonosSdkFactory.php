<?php declare(strict_types=1);

namespace Zonos\ZonosSdk;

use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;

/**
 * Factory class for creating Zonos SDK instances
 */
class ZonosSdkFactory
{
  /**
   * Create a new Zonos connector instance
   *
   * @param string $credential_token Authentication token for API access
   * @param string $base_url Base URL for API endpoints
   * @param ZonosPlatformType $platformType Platform type (default, wordpress, etc)
   * @param array $config Configuration settings
   * @return ZonosConnector
   */
  public static function create(
    string            $credential_token,
    string            $base_url,
    ZonosPlatformType $platformType = ZonosPlatformType::Default,
    array             $config = []
  ): ZonosConnector {
    $zonosConfig = new ZonosConfig($config);

    return new ZonosConnector(
      credential_token: $credential_token,
      base_url:         $base_url,
      zonosConfig:      $zonosConfig,
      platformType:     $platformType
    );
  }
}