<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Config;

/**
 * Configuration class for Zonos SDK
 * 
 * Handles configuration settings and mappings for the Zonos SDK,
 * providing a centralized way to manage entity mappings and other
 * configuration options.
 */
class ZonosConfig
{
  /**
   * Entity to configuration mappings
   *
   * @var array<string, mixed>
   */
  private array $mappings = [];

  /**
   * Create a new ZonosConfig instance
   *
   * @param array<string, mixed> $config Configuration settings
   */
  public function __construct(array $config = [])
  {
    $this->mappings = $config['mappings'] ?? [];
  }

  /**
   * Get all entity mappings
   *
   * @return array<string, mixed> Array of all configured mappings
   */
  public function getMappings(): array
  {
    return $this->mappings;
  }

  /**
   * Get the mapping configuration for a specific entity
   *
   * @param string $entity The entity type to retrieve mapping for
   * @return array<string, mixed>|null The mapping configuration if found, null otherwise
   */
  public function getMapping(string $entity): ?array
  {
    return $this->mappings[$entity] ?? null;
  }
}
