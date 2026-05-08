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
   * Free-form options keyed by name (e.g. 'amountPath').
   *
   * @var array<string, mixed>
   */
  private array $options = [];

  /**
   * Create a new ZonosConfig instance
   *
   * @param array<string, mixed> $config Configuration settings
   */
  public function __construct(array $config = [])
  {
    $this->mappings = $config['mappings'] ?? [];
    $this->options = $config['options'] ?? [];
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

  /**
   * Get a top-level option value, with a default fallback.
   *
   * @param string $key Option key
   * @param mixed $default Value to return if option is unset
   * @return mixed
   */
  public function getOption(string $key, mixed $default = null): mixed
  {
    return $this->options[$key] ?? $default;
  }
}
