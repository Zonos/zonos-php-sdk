<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Data\Enums\PartyType;
use Zonos\ZonosSdk\Data\Order;
use Zonos\ZonosSdk\Data\Party;

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
   * Store a WooCommerce order in the WordPress database
   *
   * @param Order $order_data Order data with required fields
   * @return \WC_Order The created WooCommerce order object
   * @throws InvalidArgumentException
   */
  public function storeOrder(Order $order_data): \WC_Order
  {
    // TODO: Let's move the querying of the data to here and not expect the object to arrive from somewhere
    //        might be best jus to get the order ID and perform all of the logic from here

    /** @var \WC_Order $wooOrder */
    $wooOrder = wc_create_order(
      [
        'status' => $order_data->status ?? 'pending',
      ]
    );

    if (!$wooOrder) {
      throw new InvalidArgumentException('Failed to create WooCommerce order');
    }

    foreach ($order_data->items as $item) {
      /** @var \WC_Product $product */
      $product = wc_get_product($item->productId);
      if (!$product) {
        throw new InvalidArgumentException("Product not found with ID: {$item->productId}");
        // TODO:  Let's validate if productId is defined first. We might just have the SKU configured
        //        in that case then we can use wc_get_product_id_by_sku to get the productID
      }
      $wooOrder->add_product($product, $item->quantity);
    }


    /** @var null|Party $billing_party */
    $billing_party = null;
    /** @var Party $party */
    foreach ($order_data->parties as $party) {
      if ($party->type === PartyType::PAYOR) {
        $billing_party = $party;
        break;
      }
    }

    if ($billing_party !== null) {
      $wooOrder->set_billing_first_name($billing_party->person?->firstName ?? '');
      $wooOrder->set_billing_last_name($billing_party->person?->lastName ?? '');
      $wooOrder->set_billing_company($billing_party->person?->companyName ?? '');
      $wooOrder->set_billing_address_1($billing_party->location?->line1 ?? '');
      $wooOrder->set_billing_address_2($billing_party->location?->line2 ?? '');
      $wooOrder->set_billing_city($billing_party->location?->locality ?? '');
      $wooOrder->set_billing_state($billing_party->location?->administrativeArea ?? '');
      $wooOrder->set_billing_postcode($billing_party->location?->postalCode ?? '');
      $wooOrder->set_billing_country($billing_party->location?->countryCode ?? '');
      $wooOrder->set_billing_email($billing_party->person?->email ?? '');
      $wooOrder->set_billing_phone($billing_party->person?->phone ?? '');
    }

    /** @var null|Party $shipping_party */
    $shipping_party = null;
    /** @var Party $party */
    foreach ($order_data->parties as $party) {
      if ($party->type === PartyType::DESTINATION) {
        $shipping_party = $party;
        break;
      }
    }

    if ($shipping_party !== null) {
      $wooOrder->set_shipping_first_name($shipping_party->person?->firstName ?? '');
      $wooOrder->set_shipping_last_name($shipping_party->person?->lastName ?? '');
      $wooOrder->set_shipping_company($shipping_party->person?->companyName ?? '');
      $wooOrder->set_shipping_address_1($shipping_party->location?->line1 ?? '');
      $wooOrder->set_shipping_address_2($shipping_party->location?->line2 ?? '');
      $wooOrder->set_shipping_city($shipping_party->location?->locality ?? '');
      $wooOrder->set_shipping_state($shipping_party->location?->administrativeArea ?? '');
      $wooOrder->set_shipping_postcode($shipping_party->location?->postalCode ?? '');
      $wooOrder->set_shipping_country($shipping_party->location?->countryCode ?? '');
      $wooOrder->set_shipping_phone($shipping_party->person?->phone ?? '');
    }

    if ($order_data->amountSubtotals !== null) {
      $wooOrder->set_shipping_total($order_data->amountSubtotals->shipping);
      $wooOrder->set_discount_total($order_data->amountSubtotals->discounts);
      $wooOrder->set_cart_tax($order_data->amountSubtotals->taxes);

//      if ($order_data->amountSubtotals->shipping > 0) {
//        $shipping_item = new \WC_Order_Item_Shipping();
//        $shipping_item->set_method_title(); // TODO: Let's find where we can get these two values
//        $shipping_item->set_method_id();
//        $shipping_item->set_total($order_data->amountSubtotals->shipping);
//        $wooOrder->add_item($shipping_item);
//      }

      if ($order_data->amountSubtotals->duties > 0) {
        $fee = new \WC_Order_Item_Fee();
        $fee->set_name('Duties');
        $fee->set_amount($order_data->amountSubtotals->duties);
        $fee->set_total($order_data->amountSubtotals->duties);
        $wooOrder->add_item($fee);
      }

      if ($order_data->amountSubtotals->fees > 0) {
        $fee = new \WC_Order_Item_Fee();
        $fee->set_name('Additional Fees');
        $fee->set_amount($order_data->amountSubtotals->fees);
        $fee->set_total($order_data->amountSubtotals->fees);
        $wooOrder->add_item($fee);
      }
    }

    $wooOrder->calculate_totals();
    $wooOrder->save();

    return $wooOrder;
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
   * @param int $order_id The order ID
   * @param array $order_data The order data
   */
  private function storeOrderMeta(int $order_id, array $order_data): void
  {

  }

  /**
   * Store order items
   *
   * @param int $order_id The order ID
   * @param array $items The items
   */
  private function storeOrderItems(int $order_id, array $items): void
  {

  }
}