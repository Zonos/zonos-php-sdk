<?php declare(strict_types=1);
/**
 * Main connector class for interacting with the Zonos API
 * Handles authentication and base configuration
 */

namespace Zonos\ZonosSdk\Connectors;

use Saloon\Http\Connector;
use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Services\DataMapperService;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Services\AbstractZonosService;
use Zonos\ZonosSdk\Services\ZonosServiceFactory;

/**
 * Main connector class for interacting with the Zonos API
 * Handles authentication and base configuration
 */
class ZonosConnector extends Connector
{
  private ZonosPlatformType $platformType;
  private AbstractZonosService $service;
  private DataMapperService $dataMapper;


  /**
   * Create a new Zonos connector instance
   *
   * @param string $credential_token Authentication token for API access
   * @param string $base_url Base URL for API endpoints
   * @param ZonosConfig $zonosConfig Configuration settings
   * @param ZonosPlatformType $platformType Platform type (default, wordpress, etc)
   */
  public function __construct(
    protected string      $credential_token,
    protected string      $base_url,
    protected ZonosConfig $zonosConfig,
    ZonosPlatformType     $platformType = ZonosPlatformType::Default
  ) {
    $this->platformType = $platformType;
    $this->dataMapper = new DataMapperService($zonosConfig);
    $this->service = ZonosServiceFactory::createService($this);
  }

  /**
   * Get the data mapper service instance
   *
   * @return DataMapperService
   */
  public function getDataMapper(): DataMapperService
  {
    return $this->dataMapper;
  }

  /**
   * Get the service instance
   *
   * @return AbstractZonosService
   */
  public function service(): AbstractZonosService
  {
    return $this->service;
  }

  /**
   * Get the platform type
   *
   * @return ZonosPlatformType
   */
  public function getPlatformType(): ZonosPlatformType
  {
    return $this->platformType;
  }

  /**
   * Resolve the base URL
   *
   * @return string
   */
  public function resolveBaseUrl(): string
  {
    return $this->base_url;
  }

  /**
   * Default headers for the connector
   *
   * @return array
   */
  protected function defaultHeaders(): array
  {
    return [
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'credentialToken' => $this->credential_token
    ];
  }
}