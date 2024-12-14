<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Config\ZonosConfig;

/**
 * Service for mapping data between different formats and structures
 */
class DataMapperService
{
  /**
   * Create a new DataMapperService instance
   *
   * @param ZonosConfig $config Configuration settings
   */
  public function __construct(
    private readonly ZonosConfig $config
  ) {
  }

  /**
   * Map data according to configured entity mappings
   *
   * @param string $entity The entity type to map
   * @param array $data The data to be mapped
   * @return array The mapped data
   */
  public function mapData(string $entity, array $data): array
  {
    $mapping = $this->config->getMapping($entity);

    if (!$mapping) {
      return $data;
    }

    $result = [];
    foreach ($data as $key => $value) {
      $mapped_key = $mapping[$key] ?? $key;
      $result[$mapped_key] = $value;
    }

    return $result;
  }
}