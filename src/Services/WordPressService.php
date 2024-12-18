<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Data\Enums\OrderStatus;
use Zonos\ZonosSdk\Data\Enums\PartyType;
use Zonos\ZonosSdk\Data\ExchangeRate;
use Zonos\ZonosSdk\Data\Item;
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

    $items = [];
    $cart = WC()->cart;

    foreach ($cart->get_cart() as $cart_item) {
      $product = wc_get_product($cart_item['product_id']);

      $mappedProduct = $this->data_mapper_service->mapProductData($cart_item, $product);

      array_push($items, $mappedProduct);
    }

    return $items;
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
    $this->validateExistingOrder($order_data->id, false);

    /** @var \WC_Order $wooOrder */
    $wooOrder = null;

    try {
      $wooOrder = $this->createBaseOrder($order_data->id);
      $this->addOrderItems($wooOrder, $order_data->items);
      $this->setOrderAddresses($wooOrder, $order_data->parties);
      $this->processOrderTotals($wooOrder, $order_data);

      $wooOrder->calculate_totals();
      $wooOrder->save();

      return $wooOrder;
    } catch (\Exception $e) {
      if ($wooOrder !== null) {
        $wooOrder->delete(true);
      }
      throw $e;
    }
  }

  public function updateOrder(Order $order_data): \WC_Order
  {
    try {
      /** @var \WC_Order $wooOrder */
      $wooOrder = $this->validateExistingOrder($order_data->id, true);
      if (!$wooOrder) {
        throw new InvalidArgumentException("Failed to retrieve WooCommerce order with Zonos ID {$order_data->id}");
      }

      $updates = [];

      if ($this->updateOrderTrackingNumbers($wooOrder, $order_data->shipments)) {
        $updates[] = 'tracking numbers';
      }

      if ($this->updateOrderStatus($wooOrder, $order_data->status)) {
        $updates[] = sprintf('status to %s', $order_data->status->value);
      }

      if (!empty($updates)) {
        $wooOrder->add_order_note(
          sprintf('Zonos order updated: %s', implode(', ', $updates)),
          false
        );
      }

      $wooOrder->save();
      return $wooOrder;
    } catch (\Exception $e) {
      throw new InvalidArgumentException("Failed to update order: " . $e->getMessage());
    }
  }

  /**
   * Validates order existence and returns the order if found
   *
   * @param string $zonos_order_id The order ID
   * @param bool $should_exist Whether the order should exist (true for update, false for create)
   * @return \WC_Order|null Returns the order if should_exist is true and order is found, null otherwise
   * @throws InvalidArgumentException If order existence doesn't match should_exist parameter
   */
  private function validateExistingOrder(string $zonos_order_id, bool $should_exist = false): ?\WC_Order
  {
    $existing_orders = wc_get_orders(
      [
        'meta_key' => 'zonos_order_id',
        'meta_value' => $zonos_order_id,
        'limit' => 1,
      ]
    );

    $order_exists = !empty($existing_orders);

    if ($should_exist && !$order_exists) {
      throw new InvalidArgumentException("Order with Zonos ID {$zonos_order_id} not found");
    }

    if (!$should_exist && $order_exists) {
      throw new InvalidArgumentException("Order with Zonos ID {$zonos_order_id} already exists");
    }

    return $order_exists ? $existing_orders[0] : null;
  }

  /**
   * Creates the WooCommerce order
   *
   * @param string $zonos_order_id The order ID
   * @return \WC_Order The order
   * @throws InvalidArgumentException If order failed to be created
   */
  private function createBaseOrder(string $zonos_order_id): \WC_Order
  {
    $wooOrder = wc_create_order(
      [
        'status' => 'wc-processing',
      ]
    );

    if (!$wooOrder) {
      throw new InvalidArgumentException('Failed to create WooCommerce order');
    }

    $wooOrder->update_meta_data('zonos_order_id', $zonos_order_id);
    $wooOrder->update_meta_data('order_attribution_origin', 'Zonos Checkout');

    return $wooOrder;
  }

  /**
   * Adds the product to the order
   *
   * @param \WC_Order $wooOrder The order
   * @param Item[] $items The items/products to add
   * @throws InvalidArgumentException If the product is not found
   */
  private function addOrderItems(\WC_Order $wooOrder, array $items): void
  {
    foreach ($items as $item) {
      $productId = wc_get_product_id_by_sku($item->sku);
      $product = wc_get_product($productId ?? $item->productId);

      if (!$product) {
        throw new InvalidArgumentException("Product not found by SKU: {$item->sku} or ID: {$item->productId}");
      }

      $wooOrder->add_product(
        $product,
        $item->quantity,
        [
          'subtotal' => $item->amount ?? $product->get_price(),
          'total' => $item->amount ?? $product->get_price(),
        ]
      );
    }
  }

  /**
   * Sets billing and shipping addresses to the order
   *
   * @param \WC_Order $wooOrder The order
   * @param Party[] $parties The parties/addreses
   */
  private function setOrderAddresses(\WC_Order $wooOrder, array $parties): void
  {
    $billing_party = $this->findPartyByType($parties, PartyType::PAYOR);
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

    $shipping_party = $this->findPartyByType($parties, PartyType::DESTINATION);
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
  }

  /**
   * Finds a party by type
   *
   * @param Party[] $parties The parties
   * @param PartyType $type The party type
   * @return Party|null The party or null
   */
  private function findPartyByType(array $parties, PartyType $type): ?Party
  {
    foreach ($parties as $party) {
      if ($party->type === $type) {
        return $party;
      }
    }
    return null;
  }

  /**
   * Processes the order totals
   *
   * @param \WC_Order $wooOrder The order
   * @param Order $order_data The order data
   */
  private function processOrderTotals(\WC_Order $wooOrder, Order $order_data): void
  {
    if ($order_data->amountSubtotals === null) {
      return;
    }

    $exchangeRate = $this->getExchangeRate($order_data);
    $convertAmount = function (float $amount) use ($exchangeRate): float {
      if ($exchangeRate !== null) {
        return round($amount / $exchangeRate->rate, 2);
      }
      return $amount;
    };

    $wooOrder->set_discount_total($convertAmount($order_data->amountSubtotals->discounts));
    $wooOrder->set_currency($exchangeRate ? $exchangeRate->sourceCurrencyCode : $order_data->currencyCode);

    $this->addShippingIfNeeded($wooOrder, $order_data, $convertAmount);
    $this->addFeeIfNeeded($wooOrder, 'Taxes', $order_data->amountSubtotals->taxes, $convertAmount);
    $this->addFeeIfNeeded($wooOrder, 'Duties', $order_data->amountSubtotals->duties, $convertAmount);
    $this->addFeeIfNeeded($wooOrder, 'Additional Fees', $order_data->amountSubtotals->fees, $convertAmount);
  }

  /**
   * Gets exchange rate if needed
   *
   * @param Order $order_data The order data
   * @return ExchangeRate|null The exchange rate object or null
   */
  private function getExchangeRate(Order $order_data): ?ExchangeRate
  {
    if ($order_data->root === null ||
    empty($order_data->root->exchangeRates) ||
    $order_data->currencyCode === reset($order_data->items)->currencyCode ?? '') {
      return null;
    }

    foreach ($order_data->root->exchangeRates as $rate) {
      if ($rate->targetCurrencyCode === $order_data->currencyCode) {
        return $rate;
      }
    }

    return null;
  }

  /**
   * Adds shipping to the order if needed
   *
   * @param \WC_Order $wooOrder The order
   * @param Order $order_data The order data
   * @param callable $convertAmount The converter
   */
  private function addShippingIfNeeded(\WC_Order $wooOrder, Order $order_data, callable $convertAmount): void
  {
    if ($order_data->amountSubtotals->shipping <= 0) {
      return;
    }

    $shipping_method = !empty($order_data->shipmentRatings) ? $order_data->shipmentRatings[0] : null;
    $shipping_item = new \WC_Order_Item_Shipping();
    $shipping_item->set_method_title($shipping_method?->displayName ?? 'Shipping');
    $shipping_item->set_method_id($shipping_method?->serviceLevelCode ?? 'default');
    $shipping_item->set_total($convertAmount($order_data->amountSubtotals->shipping));
    $wooOrder->add_item($shipping_item);
  }

  /**
   * Adds a fee to the order if needed
   *
   * @param \WC_Order $wooOrder The order
   * @param string $name The name
   * @param float $amount The amount
   * @param callable $convertAmount The amount converter function
   */
  private function addFeeIfNeeded(\WC_Order $wooOrder, string $name, float $amount, callable $convertAmount): void
  {
    if ($amount <= 0) {
      return;
    }

    $fee = new \WC_Order_Item_Fee();
    $fee->set_name($name);
    $fee->set_amount($convertAmount($amount));
    $fee->set_total($convertAmount($amount));
    $fee->set_tax_status('none');
    $wooOrder->add_item($fee);
  }

  /**
   * Updates tracking numbers for an order
   *
   * @param \WC_Order $wooOrder The WooCommerce order
   * @param array $shipments The shipments array from Zonos order
   * @return bool Whether any tracking numbers were added
   */
  private function updateOrderTrackingNumbers(\WC_Order $wooOrder, array $shipments): bool
  {
    if (empty($shipments)) {
      return false;
    }

    $new_tracking_numbers = [];
    foreach ($shipments as $shipment) {
      foreach ($shipment->trackingDetails as $tracking_detail) {
        $new_tracking_numbers[] = wc_clean($tracking_detail->number);
      }
    }

    $existing_tracking_string = $wooOrder->get_meta('zonos_tracking_numbers', true);
    $existing_tracking_numbers = !empty($existing_tracking_string)
      ? array_map('trim', explode(',', $existing_tracking_string))
      : [];

    sort($new_tracking_numbers);
    sort($existing_tracking_numbers);

    if ($new_tracking_numbers !== $existing_tracking_numbers) {
      $wooOrder->update_meta_data('zonos_tracking_numbers', implode(', ', $new_tracking_numbers));

      $added_numbers = array_diff($new_tracking_numbers, $existing_tracking_numbers);
      if (!empty($added_numbers)) {
        $wooOrder->add_order_note(
          sprintf(
            'New tracking number(s) added: %s',
            implode(', ', $added_numbers)
          ),
          false
        );
      }
      return true;
    }

    return false;
  }

  /**
   * Maps Zonos status to WooCommerce status
   *
   * @param OrderStatus $zonos_status The Zonos order status
   * @return string The WooCommerce status
   */
  private function mapOrderStatus(OrderStatus $zonos_status): string
  {
    return match ($zonos_status) {
      OrderStatus::COMPLETED => 'wc-completed',
      OrderStatus::CANCELED => 'wc-cancelled',
      OrderStatus::FRAUD_HOLD => 'wc-on-hold',
      OrderStatus::PAYMENT_FAILED => 'wc-failed',
      OrderStatus::PAYMENT_PENDING => 'wc-pending',
      OrderStatus::PARTIALLY_SHIPPED => 'wc-processing',
      OrderStatus::IN_TRANSIT_TO_CONSOLIDATION_CENTER => 'wc-processing',
      OrderStatus::OPEN => 'wc-processing',
    };
  }

  /**
   * Updates the order status if changed
   *
   * @param \WC_Order $wooOrder The WooCommerce order
   * @param OrderStatus $new_status The new status from Zonos
   * @return bool Whether the status was updated
   */
  private function updateOrderStatus(\WC_Order $wooOrder, OrderStatus $new_status): bool
  {
    $new_wc_status = $this->mapOrderStatus($new_status);
    $current_status = 'wc-' . $wooOrder->get_status();

    if ($current_status !== $new_wc_status) {
      $wooOrder->set_status($new_wc_status, '', true);
      return true;
    }

    return false;
  }
}