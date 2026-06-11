<?php

declare(strict_types=1);

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
  ) {}

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
        'length', 'width', 'height' => $this->mapDimension($result, $value, $cart_item, $productData),
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
          case 'addon_fields':
            // Opt-in: pull the cart line's display fields straight from
            // WooCommerce's standard woocommerce_get_item_data filter — the
            // same source the native cart/order view uses. Any add-on plugin
            // (Gravity Forms, WAPF, YITH, etc.) that registers display data is
            // surfaced with its real labels, already product-specific and free
            // of the field-id collisions that raw _gravity_form_lead lookups hit.
            $itemData = apply_filters('woocommerce_get_item_data', [], $cart_item);
            foreach ((array)$itemData as $row) {
              if (!is_array($row)) {
                continue;
              }
              // WC core uses 'key'/'display'; some add-ons use 'name'/'value'.
              $label = trim(wp_strip_all_tags((string)($row['key'] ?? $row['name'] ?? '')));
              $cleanValue = trim(wp_strip_all_tags((string)($row['display'] ?? $row['value'] ?? '')));
              if ($label !== '' && $cleanValue !== '') {
                // 'Alias: ' prefix is required: on import, WordpressService
                // strips it and uses the remainder as the order-item meta key
                // (i.e. the field label), matching the 'custom' alias path.
                $productAttributes[] = [
                  'key' => 'Alias: ' . $label,
                  'value' => $cleanValue,
                ];
              }
            }
            // $value stays null so the single-append tail below is skipped.
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
      } catch (\Throwable $e) {
        // \Throwable (not just \Exception): the addon_fields case runs
        // arbitrary third-party hook code via woocommerce_get_item_data, which
        // can throw \Error/\TypeError. Contain it per-row so one bad add-on
        // plugin can't abort the whole cart export.
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
    // Opt-in: when amountPath is configured, resolve the per-item amount from
    // the cart item directly. This covers cart-level pricing plugins (Gravity
    // Forms, Name Your Price, Dynamic Pricing, Subscriptions, etc.) without
    // changing behavior for merchants who haven't configured it.
    $amountPath = (string) $this->config->getOption('amountPath', '');
    if ($amountPath !== '') {
      $resolved = $this->resolveCartItemPath($cart_item, $amountPath);
      if (is_numeric($resolved)) {
        $amount = (float) $resolved;
        if ((bool) $this->config->getOption('amountPathIsPerLine', false)) {
          $qty = max(1, (int) ($cart_item['quantity'] ?? 1));
          return $amount / $qty;
        }
        return $amount;
      }
      $this->logger->sendLog(
        "Zonos amountPath '{$amountPath}' did not resolve to a numeric value; falling back to mapping value '{$value}'",
        LogType::ERROR
      );
      // intentional fall-through to existing mapping behavior
    }

    $price = 0;
    switch ($value) {
      case 'plugin_wapf':
        if (isset($cart_item['wapf']) && is_array($cart_item['wapf'])) {
          foreach ($cart_item['wapf'] as $wapf_field) {
            if (isset($wapf_field['values']) && is_array($wapf_field['values'])) {
              foreach ($wapf_field['values'] as $value_option) {
                $price += (float)($value_option['price'] ?? 0);
              }
            }
          }
        }

        return $price + (float)($productData['price'] ?? 0);
    }

    return (float)($productData[$value] ?? 0);
  }

  /**
   * Walk a dot-separated path into a cart item to resolve a value. When a
   * node is a WC_Data instance (WC_Product, WC_Product_Variation, etc.),
   * auto-flatten via get_data() so paths like "data.price" reach the price
   * set by set_price() at woocommerce_add_cart_item.
   *
   * @param array<string, mixed> $cart_item Cart item data
   * @param string $path Dot-separated path
   * @return mixed|null null if the path cannot be resolved
   */
  private function resolveCartItemPath(array $cart_item, string $path): mixed
  {
    $parts = explode('.', $path);
    $node = $cart_item;
    foreach ($parts as $part) {
      if ($part === '') {
        return null;
      }
      if (is_array($node)) {
        if (!array_key_exists($part, $node)) {
          return null;
        }
        $node = $node[$part];
        continue;
      }
      if ($node instanceof \WC_Data) {
        // Prefer the canonical getter so values written via set_<prop>()
        // (which live in $changes, not $data) remain reachable.
        // WC_Data::get_<prop>() delegates to get_prop(), which consults
        // $changes before $data; get_data() returns $this->data only.
        $getter = 'get_' . $part;
        if (method_exists($node, $getter)) {
          $node = $node->{$getter}();
          continue;
        }
        $data = $node->get_data();
        if (!array_key_exists($part, $data)) {
          return null;
        }
        $node = $data[$part];
        continue;
      }
      return null;
    }
    return $node;
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
   * @param array<string, mixed> $cart_item Cart item data
   * @param array<string, mixed> $productData Product data
   * @return array<string, mixed> Updated result array
   */
  private function mapDimension(array $result, string $dimension, array $cart_item, array $productData): array
  {

    $value = $productData[$dimension] ?? null;
    if ($value === null || $value === '') {
      $parentProductData = wc_get_product($cart_item['product_id'])->get_data();
      $value = $parentProductData[$dimension] ?? null;
    }

    $result['measurements'] = $this->mapMeasurement(
      $dimension,
      (float) $value,
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
