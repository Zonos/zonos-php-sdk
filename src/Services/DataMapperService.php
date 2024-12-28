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
      if ($value != '') $result[$key] = $data[$value];
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
    if ($value) {
      $result[] = array(
        "source" => "USER_PROVIDED",
        "type" => $valueToType[$key],
        "unitOfMeasure" => $unit,
        "value" => (float)$value,
      );
    }

    return $result;
  }

  private function mapProductAttributes($attributes, $product, $cart_item)
  {
    $productAttributes = [];
    $variation = $cart_item['variation'];
    foreach ($attributes as $attribute) {
      $productAttributes[] = array(
        "key" => $attribute,
        "value" => $variation ? $variation['attribute_pa_'.$attribute] : $product->get_attribute($attribute),
      );
    }

    return $productAttributes;
  }

  private function mapByValue($key, $value, $result, $product, $productData, $cart_item)
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
        if ($value) {
          $result[$key] = $this->mapProductAttributes(explode(',', $value), $product, $cart_item);
        }
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
      if ($value != '') $productData[$key] = $value;
    }

    $mapping = $this->config->getMapping('product');

    $result = [
      "measurements" => [],
    ];

    foreach ($mapping as $key => $value) {
      if ($value != '') {

        switch ($value) {
          case 'quantity':
            $result[$key] = $cart_item['quantity'];
            break;
          case 'image_id':
            $imageUrl = wp_get_attachment_image_url($productData[$value]);
            if ($imageUrl != false) {
              $result[$key] = $imageUrl;
            }
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
            $result = $this->mapByValue($key, $value, $result, $product, $productData, $cart_item);
            break;
        }
      }
    }

    return $result;
  }
}