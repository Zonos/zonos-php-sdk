<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Config\ZonosConfig;

/**
 * Service for mapping data between different formats and structures
 *
 * Handles data transformation between WooCommerce and Zonos formats,
 * including product data, measurements, and attributes mapping.
 */
class DataMapperService
{
  /**
   * Create a new DataMapperService instance
   *
   * @param ZonosConfig $config Configuration settings for data mapping
   */
  public function __construct(
    private readonly ZonosConfig $config
  ) {
  }

  /**
   * Map data according to configured entity mappings
   *
   * @param string $entity The entity type to map
   * @param array<string, mixed> $data The data to be mapped
   * @return array<string, mixed> The mapped data
   */
  public function mapData(string $entity, array $data): array
  {
    $mapping = $this->config->getMapping($entity);
    if (!$mapping) {
      return $data;
    }

    $result = [];
    foreach ($mapping as $key => $value) {
      if ($value !== '') {
        $result[$key] = $data[$value] ?? null;
      }
    }

    return $result;
  }

  /**
   * Map product data from WooCommerce to Zonos format
   *
   * @param array<string, mixed> $cart_item The WooCommerce cart item data
   * @param \WC_Product $product The WooCommerce product instance
   * @return array<string, mixed> The mapped product data in Zonos format
   * @throws InvalidArgumentException When required product data is missing
   */
  public function mapProductData(array $cart_item, \WC_Product $product): array
  {
    $productData = $product->get_data();
    $mapping = $this->config->getMapping('product');
    if (!$mapping) {
      throw new InvalidArgumentException('Product mapping configuration is missing');
    }

    $result = [
      'measurements' => [],
    ];

    foreach ($mapping as $key => $value) {
      if ($value === '') {
        continue;
      }

      $result = match ($value) {
        'quantity' => $this->mapQuantity($result, $key, $cart_item),
        'image_id' => $this->mapImage($result, $key, (int)$productData[$value] ?? null),
        'length', 'width', 'height' => $this->mapDimension($result, $value, (float)$productData[$value] ?? null),
        'weight' => $this->mapWeight($result, (float)$productData[$value] ?? null),
        default => $this->mapByValue($key, $value, $result, $product, $productData, $cart_item),
      };
    }

    return $result;
  }

  /**
   * Map a measurement value to Zonos format
   *
   * @param string $type The measurement type (length, width, height, weight)
   * @param float|null $value The measurement value
   * @param array<string, mixed> $measurements Existing measurements array
   * @param string $unit The unit of measurement
   * @return array<string, mixed> Updated measurements array
   */
  private function mapMeasurement(string $type, ?float $value, array $measurements, string $unit): array
  {
    if ($value === null) {
      return $measurements;
    }

    $valueToType = [
      'length' => 'LENGTH',
      'width' => 'WIDTH',
      'weight' => 'WEIGHT',
      'height' => 'HEIGHT',
    ];

    $measurements[] = [
      'source' => 'USER_PROVIDED',
      'type' => $valueToType[$type],
      'unitOfMeasure' => $unit,
      'value' => (float)$value,
    ];

    return $measurements;
  }

  /**
   * Map product attributes to Zonos format
   *
   * @param array<string> $attributes The attribute keys to map
   * @param \WC_Product $product The WooCommerce product
   * @param array<string, mixed> $cart_item The cart item data
   * @return array<int, array<string, string>> The mapped attributes
   */
  private function mapProductAttributes(array $attributes, \WC_Product $product, array $cart_item): array
  {
    $productAttributes = [];

    foreach ($attributes as $attribute) {
      $parts = explode('.', $attribute);
      $value = $cart_item;
      $found = true;

      foreach ($parts as $part) {
        if (isset($value[$part])) {
          $value = $value[$part];
        } else {
          $value = null;
          $found = false;
          break;
        }
      }

      if (!$found) {

        $variation = $cart_item['variation'] ?? null;

        $value = $variation
          ? ($variation['attribute_pa_' . $attribute] ?? null)
          : $product->get_attribute($attribute);
      }


      if ($value) {
        $productAttributes[] = [
          'key' => $attribute,
          'value' => $value,
        ];
      }
    }

    return $productAttributes;
  }

  /**
   * Map a specific value based on its key and configuration
   *
   * @param string $key The target key
   * @param string $value The source value or path
   * @param array<string, mixed> $result The current result array
   * @param \WC_Product $product The WooCommerce product
   * @param array<string, mixed> $productData The product data
   * @param array<string, mixed> $cart_item The cart item data
   * @return array<string, mixed> Updated result array
   */
  private function mapByValue(
    string      $key,
    string      $value,
    array       $result,
    \WC_Product $product,
    array       $productData,
    array       $cart_item
  ): array {
    if ($value && str_contains($value, '.')) {
      $path = explode('.', $value);
      if ($path[0] === 'attributes') {
        $result[$key] = $product->get_attribute($path[1]);
        return $result;
      }
    }

    $result[$key] = match ($key) {
      'attributes' => $value ? $this->mapProductAttributes(explode(',', $value), $product, $cart_item) : [],
      'currencyCode' => $value,
      'amount' => (float)($productData[$value] ?? 0),
      'productId' => (string)($productData[$value] ?? ''),
      'hsCode' => $productData[$value] ?? '',
      default => $productData[$value] ?? $value,
    };

    return $result;
  }

  /**
   * Map quantity from cart item
   *
   * @param array<string, mixed> $result Current result array
   * @param string $key Target key
   * @param array<string, mixed> $cart_item Cart item data
   * @return array<string, mixed> Updated result array
   */
  private function mapQuantity(array $result, string $key, array $cart_item): array
  {
    $result[$key] = $cart_item['quantity'] ?? 0;
    return $result;
  }

  /**
   * Map product image
   *
   * @param array<string, mixed> $result Current result array
   * @param string $key Target key
   * @param int|null $image_id Product image ID
   * @return array<string, mixed> Updated result array
   */
  private function mapImage(array $result, string $key, ?int $image_id): array
  {
    if ($image_id !== null) {
      $imageUrl = wp_get_attachment_image_url($image_id);
      if ($imageUrl !== false) {
        $result[$key] = $imageUrl;
      }
    }
    return $result;
  }

  /**
   * Map dimension measurement
   *
   * @param array<string, mixed> $result Current result array
   * @param string $dimension Dimension type (length, width, height)
   * @param float|null $value Dimension value
   * @return array<string, mixed> Updated result array
   */
  private function mapDimension(array $result, string $dimension, ?float $value): array
  {
    $result['measurements'] = $this->mapMeasurement(
      $dimension,
      $value,
      $result['measurements'],
      get_option('zonos_length_unit_measure') ?? ''
    );
    return $result;
  }

  /**
   * Map weight measurement
   *
   * @param array<string, mixed> $result Current result array
   * @param float|null $value Weight value
   * @return array<string, mixed> Updated result array
   */
  private function mapWeight(array $result, ?float $value): array
  {
    $result['measurements'] = $this->mapMeasurement(
      'weight',
      $value,
      $result['measurements'],
      get_option('zonos_weight_unit_measure') ?? ''
    );
    return $result;
  }
}
