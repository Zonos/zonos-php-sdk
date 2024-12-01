<?php declare(strict_types=1);

namespace Zonos\ZonosSdk;

use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Enums\ZonosPlatformType;
use Zonos\ZonosSdk\Services\AbstractZonosService;
use Zonos\ZonosSdk\Services\DataMapperService;
use Zonos\ZonosSdk\Services\ZonosServiceFactory;

/**
 * Factory class for creating Zonos SDK instances
 */
class ZonosSdk
{
  private ZonosConnector $connector;
  private AbstractZonosService $service;

  public function __construct(
    string            $credential_token,
    string            $base_url,
    array             $config = [],
    ZonosPlatformType $platform_type = ZonosPlatformType::Default
  ) {
    $zonos_config = new ZonosConfig($config);
    $data_mapper_service = new DataMapperService($zonos_config);
    $this->connector = new ZonosConnector(
      credential_token: $credential_token,
      base_url:         $base_url,
    );
    $this->service = ZonosServiceFactory::createService($platform_type, $this->connector, $data_mapper_service);
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

  public function connector(): ZonosConnector
  {
    return $this->connector;
  }
}