<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Utils\DataDogLogger;

/**
 * Factory for creating platform-specific Zonos service instances
 *
 * Provides a centralized way to create the appropriate service implementation
 * based on the platform type (e.g., WordPress, Magento, etc.).
 */
class ZonosServiceFactory
{
  /**
   * Create a new Zonos service instance for the specified platform
   *
   * @param ZonosPlatformType $platformType The type of platform to create service for
   * @param ZonosConnector $connector The Zonos API connector instance
   * @param DataMapperService $dataMapperService Service for mapping data between systems
   * @return AbstractZonosService The platform-specific service implementation
   * @throws InvalidArgumentException When an unsupported platform type is provided
   */
  public static function createService(
    ZonosPlatformType $platformType,
    ZonosConnector    $connector,
    DataMapperService $dataMapperService,
    DataDogLogger     $logger,
  ): AbstractZonosService {
    return match ($platformType) {
      ZonosPlatformType::Wordpress => new WordPressService(
        connector:         $connector,
        dataMapperService: $dataMapperService,
        logger:            $logger
      ),
      default => throw new InvalidArgumentException('Unsupported platform type: ' . $platformType->value),
    };
  }
}
