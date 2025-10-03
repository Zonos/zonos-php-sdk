<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use http\Exception\RuntimeException;
use InvalidArgumentException;
use Zonos\ZonosSdk\Config\ZonosConfig;
use Zonos\ZonosSdk\Enums\LogType;
use Zonos\ZonosSdk\Utils\DataDogLogger;

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
    private readonly ZonosConfig   $config,
    private readonly DataDogLogger $logger
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
      $this->logger->sendLog('Product mapping configuration is missing', LogType::ERROR);
      throw new InvalidArgumentException('Product mapping configuration is missing');
    }

    $result = [
      'measurements' => [],
      'attributes' => [],
    ];

    foreach ($mapping as $key => $value) {
      if ($value === '') {
        continue;
      }

      $result = match ($value) {
        'quantity' => $this->mapQuantity($result, $key, $cart_item),
        'image_id' => $this->mapImage($result, $key, (int)$product->get_image_id() ?? null),
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
      'type' => $valueToType[$type],
      'unitOfMeasure' => $unit,
      'value' => (float)$value ?? 0.0,
    ];

    return $measurements;
  }

  /**
   * Map product attributes to Zonos format
   *
   * @param array<int, array{type: string, value: string, alias?: string}> $attributes The attribute keys to map
   * @param \WC_Product $product The WooCommerce product
   * @param array<string, mixed> $cart_item The cart item data
   * @return array<int, array<string, string>> The mapped attributes
   */
  private function mapProductAttributes(array $attributes, \WC_Product $product, array $cart_item): array
  {
    $productAttributes = [];

    foreach ($attributes as $attribute) {
      try {
        $value = null;
        switch ($attribute['type']) {
          case 'default_attributes':
            $variation = $cart_item['variation'] ?? null;
            $value = $variation ? ($variation['attribute_pa_' . $attribute['value']] ?? ($variation['attribute_' . $attribute['value']] ?? null)) : $product->get_attribute($attribute['value']);
            break;
          case 'custom':
            $parts = explode('.', $attribute['value']);

            if (!$parts) {
              throw new RuntimeException('Error mapping attribute ' . $attribute);
            }

            $value = $cart_item;
            foreach ($parts as $part) {
              if (isset($value[$part])) {
                $value = $value[$part];
              }
            }

            if (is_array($value) || is_object($value)) {
              $value = null;
            } elseif ($value) {
              $value = (string)$value;
            }
            break;
        }

        if ($value) {
          $key = $attribute['value'];
          if ($attribute['alias']) {
            $key = 'Alias: ' . $attribute['alias'];
          }

          $productAttributes[] = [
            'key' => $key,
            'value' => $value,
          ];
        }
      } catch (\Exception $e) {
        $this->logger->sendLog('Error mapping attribute ' . $attribute, LogType::ERROR);
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
    string       $key,
    string|array $value,
    array        $result,
    \WC_Product  $product,
    array        $productData,
    array        $cart_item
  ): array {
    try {
      if ($value && is_string($value) && str_contains($value, '.')) {
        $path = explode('.', $value);

        if (!$path) {
          throw new Exception('Error parsing map value');
        }

        if ($path[0] === 'attributes') {
          $result[$key] = $product->get_attribute($path[1]);
          return $result;
        }
      }

      $result[$key] = match ($key) {
        'attributes' => is_array($value) && array_is_list($value) ? $this->mapProductAttributes($value, $product, $cart_item) : [],
        'currencyCode' => $value,
        'amount' => $this->mapAmount($productData, $value, $cart_item),
        'productId' => (string)($productData[$value] ?? ''),
        'hsCode' => $productData[$value] ?? '',
        default => $productData[$value] ?? $value,
      };
    } catch (\Exception $e) {
      $this->logger->sendLog('Error parsing map [' . $key . '] with value: ' . $value, LogType::ERROR);
    }
    return $result;
  }

  private function mapAmount(array $productData, string $value, array $cart_item): float
  {
    $price = 0;
    switch ($value) {
      case 'plugin_wapf':
        if (isset($cart_item['wapf']) && is_array($cart_item['wapf'])) {
          foreach ($cart_item['wapf'] as $extra_item) {
            $price_item_list = $extra_item['price'];
            if (isset($price_item_list) && is_array($price_item_list)) {
              foreach($price_item_list as $price_item) {
                $price += (float)($price_item['value'] ?? 0);
              }
            }
          }
        }

        return $price + (float)($productData['price'] ?? 0);
    }

    return (float)($productData[$value] ?? 0);
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
      get_option('zonosch_length_unit_measure') ?? ''
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
      get_option('zonosch_weight_unit_measure') ?? ''
    );
    return $result;
  }
}
