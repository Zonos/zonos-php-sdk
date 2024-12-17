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

    foreach ($mapping as $key => $value) {
      $result[$key] = $data[$value];
    }

    return $result;
  }

  private function mapMeasurement($key, $value, $measurement, $unit)
  {
    $valueToType = [
      "length" => "LENGTH",
      "width" => "WIDTH",
      "weight" => "WEIGHT",
      "height" => "HEIGHT",
    ];
    $result = $measurement ?? [];
    array_push($result, array(
      "source" => "USER_PROVIDED",
      "type" => $valueToType[$key],
      "unitOfMeasure" => $unit,
      "value" => +$value,
    ));

    return $result;
  }

  private function mapProductAttributes($attributes, $product)
  {
    $productAttributes = [];
    foreach ($attributes as $attribute) {
      array_push($productAttributes, array("key" => $attribute, "value" => $product->get_attribute($attribute)));
    }

    return $productAttributes;
  }

  private function mapByValue($key, $value, $result, $product, $productData)
  {
    if ($value && str_contains($value, '.')) {
      $path = explode('.', $value);
      if ($path[0] === 'attributes') {
        $result[$key] = $product->get_attribute($path[1]);
        return $result;
      }
    }

    switch ($key) {
      case 'attributes':
        $result[$key] = $this->mapProductAttributes(explode(',', $value), $product);
        return $result;
      case 'currencyCode':
        $result[$key] = $value;
        return $result;
      case 'amount':
        $result[$key] = +$productData[$value];
        return $result;
      case 'productId':
        $result[$key] = "$productData[$value]";
        return $result;
      case 'hsCode':
        $result[$key] = $productData[$value] ?? "";
        return $result;
      default:
        $result[$key] = $productData[$value] ?? $value;
        return $result;
    }
  }

  public function mapProductData($cart_item, $product): array
  {
    $productData = [];
    foreach ($product->get_data() as $key => $value) {
      $productData[$key] = $value;
    }

    $mapping = $this->config->getMapping('product');

    $result = [
      "measurements" => [],
    ];

    foreach ($mapping as $key => $value) {
      switch($value) {
        case 'quantity':
          $result[$key] = $cart_item['quantity'];
          break;
        case 'image_id':
          $result[$key] = wp_get_attachment_image_url($productData[$value]);
          break;
        case 'length':
        case 'width':
        case 'height':
          $result['measurements'] = $this->mapMeasurement($value, $productData[$value], $result['measurements'], get_option('zonos_length_unit_measure'));
          break;
        case 'weight':
          $result['measurements'] = $this->mapMeasurement($value, $productData[$value], $result['measurements'], get_option('zonos_weight_unit_measure'));
          break;
        default:
          $result = $this->mapByValue($key, $value, $result, $product, $productData);
          break;
      }
    }

    return $result;
  }
}