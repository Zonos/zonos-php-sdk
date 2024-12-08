<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Config;

/**
 * Configuration class for Zonos SDK
 */
class ZonosConfig
{
  private array $mappings = [];

  /**
   * Create a new ZonosConfig instance
   *
   * @param array $config Configuration settings
   */
  public function __construct(array $config = [])
  {
    $this->mappings = $config['mappings'] ?? [];
  }

  /**
   * Get the mappings
   *
   * @return array
   */
  public function getMappings(): array
  {
    return $this->mappings;
  }

  /**
   * Get the mapping for a specific entity
   *
   * @param string $entity The entity type
   * @return array|null
   */
  public function getMapping(string $entity): ?array
  {
    return $this->mappings[$entity] ?? null;
  }
}
