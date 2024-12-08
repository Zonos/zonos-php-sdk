<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Connectors\ZonosConnector;

/**
 * Factory class for creating Zonos service instances
 */
class ZonosServiceFactory
{
  /**
   * Create a new Zonos service instance
   *
   * @param ZonosConnector $connector The connector instance
   * @return AbstractZonosService
   */
  public static function createService(ZonosPlatformType $zonos_platform_type, ZonosConnector $connector, DataMapperService $data_mapper_service): AbstractZonosService
  {
    return match ($zonos_platform_type) {
      ZonosPlatformType::Wordpress => new WordPressService($connector, $data_mapper_service),
      default => throw new \InvalidArgumentException('Unsupported platform type'),
    };
  }
}
