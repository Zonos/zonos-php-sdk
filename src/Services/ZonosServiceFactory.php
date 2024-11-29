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
  public static function createService(ZonosConnector $connector): AbstractZonosService
  {
    return match ($connector->getPlatformType()) {
      ZonosPlatformType::Wordpress => new WordPressService($connector),
      default => throw new \InvalidArgumentException('Unsupported platform type'),
    };
  }
}
