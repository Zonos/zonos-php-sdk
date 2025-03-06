<?php declare(strict_types=1);

namespace Zonos\ZonosSdk;

define('VERSION', '1.0.0');

use InvalidArgumentException;
use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Services\AbstractZonosService;
use Zonos\ZonosSdk\Services\DataMapperService;
use Zonos\ZonosSdk\Services\ZonosServiceFactory;
use Zonos\ZonosSdk\Utils\DataDogLogger;

/**
 * Main entry point for the Zonos SDK
 *
 * Provides access to various Zonos services and connectors, handling
 * initialization and configuration of the SDK components.
 */
class ZonosSdk
{
  /**
   * @var ZonosConnector The main Zonos API connector
   */
  private readonly ZonosConnector $connector;

  /**
   * @var AbstractZonosService The platform-specific service implementation
   */
  private readonly AbstractZonosService $service;

  /**
   * @var DataDogLogger The logger service
   */
  private readonly DataDogLogger $logger;

  /**
   * Create a new ZonosSdk instance
   *
   * @param string $credentialToken Authentication token for API access
   * @param string $baseUrl Base URL for main API endpoints
   * @param array<string, mixed> $config Additional configuration options
   * @param ZonosPlatformType $platformType The type of platform being integrated
   * @param string $platformVersion The version of platform being integrated
   * @throws InvalidArgumentException When invalid configuration is provided
   */
  public function __construct(
    string            $credentialToken,
    string            $baseUrl,
    array             $config = [],
    ZonosPlatformType $platformType = ZonosPlatformType::Default,
    string            $platformVersion = '',
    bool              $debugMode = false,
  ) {
    $clientHeaders = [
      'x-client-name' => $platformType->value . ' - (zonos-sdk)',
      'x-client-version' => $platformVersion . ' (sdk:' . VERSION . ')'
    ];

    $zonosConfig = new ZonosConfig(
      config: $config
    );

    $this->logger = new DataDogLogger(
      credentialToken: $credentialToken,
      clientHeaders:   $clientHeaders,
      debugMode:       $debugMode,
    );

    $dataMapperService = new DataMapperService(
      config: $zonosConfig,
      logger: $this->logger
    );

    $this->connector = new ZonosConnector(
      credentialToken: $credentialToken,
      baseUrl:         $baseUrl,
      clientHeaders:   $clientHeaders,
    );

    $this->service = ZonosServiceFactory::createService(
      platformType:      $platformType,
      connector:         $this->connector,
      dataMapperService: $dataMapperService,
      logger:            $this->logger
    );
  }

  /**
   * Get the platform-specific service implementation
   *
   * @return AbstractZonosService The service instance
   */
  public function service(): AbstractZonosService
  {
    return $this->service;
  }

  /**
   * Get the main API connector
   *
   * @return ZonosConnector The connector instance
   */
  public function connector(): ZonosConnector
  {
    return $this->connector;
  }

  /**
   * Get the logger service
   *
   * @return DataDogLogger The logger instance
   */
  public function logger(): DataDogLogger
  {
    return $this->logger;
  }
}