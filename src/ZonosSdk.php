<?php declare(strict_types=1);

namespace Zonos\ZonosSdk;

use InvalidArgumentException;
use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\Auth\AuthConnector;
use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Services\AbstractZonosService;
use Zonos\ZonosSdk\Services\DataMapperService;
use Zonos\ZonosSdk\Services\ZonosAuthService;
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
   * @var AuthConnector The authentication API connector
   */
  private readonly AuthConnector $authConnector;

  /**
   * @var AbstractZonosService The platform-specific service implementation
   */
  private readonly AbstractZonosService $service;

  /**
   * @var ZonosAuthService The authentication service
   */
  private readonly ZonosAuthService $authService;

  /**
   * @var DataDogLogger The logger service
   */
  private readonly DataDogLogger $logger;

  /**
   * Create a new ZonosSdk instance
   *
   * @param string $credentialToken Authentication token for API access
   * @param string $baseUrl Base URL for main API endpoints
   * @param string $authUrl Base URL for authentication endpoints
   * @param array<string, mixed> $config Additional configuration options
   * @param ZonosPlatformType $platformType The type of platform being integrated
   * @throws InvalidArgumentException When invalid configuration is provided
   */
  public function __construct(
    string            $credentialToken,
    string            $baseUrl,
    string            $authUrl,
    array             $config = [],
    ZonosPlatformType $platformType = ZonosPlatformType::Default
  ) {
    $zonosConfig = new ZonosConfig($config);
    $dataMapperService = new DataMapperService($zonosConfig);

    $this->logger = new DataDogLogger();
    $this->connector = new ZonosConnector(
      credentialToken: $credentialToken,
      baseUrl:         $baseUrl,
    );

    $this->authConnector = new AuthConnector(
      credentialToken: $credentialToken,
      baseUrl:         $authUrl,
    );

    $this->authService = new ZonosAuthService($zonosConfig, $this->authConnector);
    $this->service = ZonosServiceFactory::createService($platformType, $this->connector, $dataMapperService);
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
   * Get the authentication service
   *
   * @return ZonosAuthService The auth service instance
   */
  public function authService(): ZonosAuthService
  {
    return $this->authService;
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
   * Get the authentication API connector
   *
   * @return AuthConnector The auth connector instance
   */
  public function authConnector(): AuthConnector
  {
    return $this->authConnector;
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