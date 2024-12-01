<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\ZonosConnector;

/**
 * Service for handling WordPress-specific operations
 */
class WordPressService extends AbstractZonosService
{
  /**
   * Constructor for the WordPressService
   *
   * @param ZonosConnector $connector The connector instance
   */
  public function __construct(
    ZonosConnector    $connector,
    DataMapperService $data_mapper_service
  ) {
    parent::__construct($connector, $data_mapper_service);
  }

  /**
   * Export a WooCommerce order from the WordPress database
   * in the Zonos Format
   *
   * @return array Array of mapped product data
   */
  public function exportOrder(): array
  {
    if (!function_exists('WC')) {
      throw new \RuntimeException('WooCommerce is not active');
    }

    $products = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
      $product = wc_get_product($cart_item['product_id']);

      $rawProductData = [
        'product_id' => $product->get_id(),
        'url' => get_permalink($product->get_id()),
        'product_cat' => $this->getProductCategories($product),
        'image_id' => wp_get_attachment_image_url($product->get_image_id()),
      ];

      $methods = get_class_methods($product);
      foreach ($methods as $method) {
        if (str_starts_with($method, 'get_')) {
          $key = substr($method, 4); // Drop the get_ prefix
          $rawProductData[$key] = $product->$method();
        }
      }

      if (!empty($product->get_attributes())) {
        $rawProductData['itemCustomization'] = json_encode($product->get_attributes());
      }

      $mappedProduct = $this->dataMapper->mapData('product', $rawProductData);


      $products[] = $mappedProduct;
    }

    return $products;
  }

  /**
   * Get formatted product categories
   *
   * @param \WC_Product $product
   * @return string Comma-separated category slugs
   */
  private function getProductCategories(\WC_Product $product): string
  {
    $categories = get_the_terms($product->get_id(), 'product_cat');
    if (!$categories || is_wp_error($categories)) {
      return '';
    }

    return implode(',', array_map(fn($category) => $category->slug, $categories));
  }

  /**
   * Store a WooCommerce order in the WordPress database
   *
   * @param array $orderData Order data with required fields
   * @return int The ID of the created order
   * @throws InvalidArgumentException
   */
  public function storeOrder(array $orderData): int
  {

    return 0;
  }

  /**
   * Validate the order data
   *
   * @param array $data The order data
   * @param array $required The required fields
   * @param string $prefix The prefix for the error message
   * @throws InvalidArgumentException
   */
  private function validateOrderData(array $data, array $required, string $prefix = ''): void
  {

  }

  /**
   * Store order meta data
   *
   * @param int $orderId The order ID
   * @param array $orderData The order data
   */
  private function storeOrderMeta(int $orderId, array $orderData): void
  {

  }

  /**
   * Store order items
   *
   * @param int $orderId The order ID
   * @param array $items The items
   */
  private function storeOrderItems(int $orderId, array $items): void
  {

  }
} 