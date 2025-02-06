<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use InvalidArgumentException;
use RuntimeException;
use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Cart;
use Zonos\ZonosSdk\Data\Checkout\CartAdjustment;
use Zonos\ZonosSdk\Data\Checkout\Enums\OrderStatus;
use Zonos\ZonosSdk\Data\Checkout\Enums\PartyType;
use Zonos\ZonosSdk\Data\Checkout\ExchangeRate;
use Zonos\ZonosSdk\Data\Checkout\Item;
use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Data\Checkout\Party;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\CartAdjustmentInput;
use Zonos\ZonosSdk\Requests\Inputs\Checkout\CartCreateInput;
use Zonos\ZonosSdk\Data\Checkout\Enums\CartAdjustmentType;
use Zonos\ZonosSdk\Data\Checkout\Enums\CurrencyCode;
use Zonos\ZonosSdk\Utils\DataDogLogger;

/**
 * WordPress-specific implementation of Zonos service
 *
 * Handles integration between Zonos and WooCommerce/WordPress,
 * including order creation, updates, and data mapping.
 */
class WordPressService extends AbstractZonosService
{
  /**
   * Constructor for the WordPressService
   *
   * @param ZonosConnector $connector The connector instance
   * @param DataMapperService $dataMapperService Service for mapping data between systems
   */
  public function __construct(
    ZonosConnector    $connector,
    DataMapperService $dataMapperService,
    DataDogLogger     $logger,
  ) {
    parent::__construct(
      connector:         $connector,
      dataMapperService: $dataMapperService,
      logger:            $logger,
    );
  }

  public function getCredentialToken(string $type, string $mode): string
  {
    return $this->connector->pluginCredential(['type' => $type, 'mode' => $mode, 'source' => $this->connector->getSource()])?->get('id')?->id ?? '';
  }

  /**
   * Export a WooCommerce order from the WordPress database
   * and create a cart in Zonos
   *
   * @return string|null The cart ID
   * @throws RuntimeException When WooCommerce is not active
   */
  public function exportOrder(): ?string
  {
    if (!function_exists('WC')) {
      throw new RuntimeException('WooCommerce is not active');
    }
    $cart = WC()->cart;

    $items = [];
    $adjustments = [];
    $appliedCoupons = $cart->get_applied_coupons();

    foreach ($cart->get_cart() as $cartItem) {
      try {
        $product = wc_get_product($cartItem['product_id']);
        if (!empty($cartItem['variation_id'])) {
          $product = wc_get_product($cartItem['variation_id']);
        }
        $mappedProduct = $this->dataMapperService->mapProductData($cartItem, $product);

        $mappedProduct['metadata'] = [['key' => 'raw_cart_item', 'value' => json_encode($cartItem)]];

        $items[] = $mappedProduct;

        foreach ($appliedCoupons as $couponCode) {
          try {
            $coupon = new \WC_Coupon($couponCode);
            $discountApplied = $this->getDiscountApplied($coupon, $cartItem);
            if ($discountApplied !== null) {
              $type = CartAdjustmentType::ITEM;
              if ($coupon->get_discount_type() === 'fixed_cart') {
                $type = CartAdjustmentType::CART_TOTAL;
              }

              $adjustment = new CartAdjustmentInput(
                amount:       $discountApplied,
                currencyCode: CurrencyCode::from($mappedProduct['currencyCode']),
                description:  $couponCode,
                productId:    $mappedProduct['productId'] ?? null,
                sku:          $mappedProduct['sku'] ?? null,
                type:         $type,
              );

              $adjustments[] = $adjustment->toArray();
            }
          } catch (\Exception $e) {
            $this->logger->sendLog('Error processing item coupon in export order: '.$e->getMessage());
          }
        }
      } catch (\Exception $e) {
        $this->logger->sendLog('Error processing item in export order: '.$e->getMessage());
      }
    }

    $cartCreateInput = CartCreateInput::fromArray(
      [
        'adjustments' => $adjustments,
        'items' => $items,
      ]
    );

    $cart = $this->connector->cartCreate($cartCreateInput)?->get('id') ?? null;

    if ($cart === null) {
      throw new InvalidArgumentException('Failed to create cart');
    }

    return $cart->id;
  }

  /**
   * Get the discount applied to a cart item
   *
   * @param \WC_Coupon $coupon The coupon
   * @param array $cartItem The cart item
   * @return float|null The discount amount or null
   */
  private function getDiscountApplied(\WC_Coupon $coupon, array $cartItem): ?float
  {
    $isValidForProduct = $coupon->is_valid_for_product($cartItem['data']);
    if ($isValidForProduct || !$isValidForProduct && $coupon->get_discount_type() === 'fixed_cart') {
      switch ($coupon->get_discount_type()) {
        case 'percent':
          return -1 * (float)($coupon->get_amount() / 100) * $cartItem['line_subtotal'];
        case 'fixed_product':
          return -1 * (float)($coupon->get_amount() * $cartItem['quantity']);
        case 'fixed_cart':
          $cartSubtotal = WC()->cart->subtotal;
          $proportion = $cartItem['line_subtotal'] / $cartSubtotal;
          return -1 * (float)($coupon->get_amount() * $proportion);
        default:
          return null;
      }
    }
    return null;
  }

  /**
   * Store a Zonos order in WooCommerce
   *
   * @param Order $orderData The order data from Zonos
   * @param Cart $cartData The cart data from Zonos
   * @return \WC_Order The created WooCommerce order
   * @throws InvalidArgumentException When order creation fails
   * @throws RuntimeException When WooCommerce is not active
   */
  public function storeOrder(Order $orderData, Cart $cartData): \WC_Order
  {
    if (!function_exists('WC')) {
      throw new RuntimeException('WooCommerce is not active');
    }

    $this->validateExistingOrder($orderData->zonosOrderId, false);

    /** @var \WC_Order|null $wooOrder */
    $wooOrder = null;

    try {
      $wooOrder = $this->createBaseOrder($orderData->zonosOrderId);
      $this->addOrderItems($wooOrder, $orderData->items, $cartData->adjustments);
      $this->setOrderAddresses($wooOrder, $orderData->parties);
      $this->processOrderTotals($wooOrder, $orderData, $cartData->adjustments);
      $wooOrder->save();

      return $wooOrder;
    } catch (\Exception $e) {
      if ($wooOrder !== null) {
        $wooOrder->delete(true);
      }
      $this->logger->sendLog('Error creating order: '.$e->getMessage());
      throw new InvalidArgumentException('Failed to create order: ' . $e->getMessage());
    }
  }

  /**
   * Update an existing WooCommerce order with Zonos data
   *
   * @param Order $orderData The updated order data from Zonos
   * @return \WC_Order The updated WooCommerce order
   * @throws InvalidArgumentException When order update fails
   * @throws RuntimeException When WooCommerce is not active
   */
  public function updateOrder(Order $orderData): \WC_Order
  {
    if (!function_exists('WC')) {
      throw new RuntimeException('WooCommerce is not active');
    }

    try {
      /** @var \WC_Order|null $wooOrder */
      $wooOrder = $this->validateExistingOrder($orderData->zonosOrderId, true);
      if (!$wooOrder) {
        throw new InvalidArgumentException("Failed to retrieve WooCommerce order with Zonos order number {$orderData->zonosOrderId}");
      }

      $updates = [];

      if ($this->updateOrderTrackingNumbers($wooOrder, $orderData->shipments)) {
        $updates[] = 'tracking numbers';
      }

      if ($this->setOrderStatus($wooOrder, $orderData->status)) {
        $updates[] = sprintf('status to %s', $orderData->status->value);
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
   * @param string $zonosOrderId The order ID
   * @param bool $shouldExist Whether the order should exist (true for update, false for create)
   * @return \WC_Order|null Returns the order if should_exist is true and order is found, null otherwise
   * @throws InvalidArgumentException If order existence doesn't match should_exist parameter
   */
  private function validateExistingOrder(string $zonosOrderId, bool $shouldExist = false): ?\WC_Order
  {
    $existing_orders = wc_get_orders(
      [
        'meta_key' => 'Zonos order number',
        'meta_value' => $zonosOrderId,
        'limit' => 1,
      ]
    );

    $orderExists = !empty($existing_orders);

    if ($shouldExist && !$orderExists) {
      throw new InvalidArgumentException("Order with Zonos ID {$zonosOrderId} not found");
    }

    if (!$shouldExist && $orderExists) {
      throw new InvalidArgumentException("Order with Zonos ID {$zonosOrderId} already exists");
    }

    return $orderExists ? $existing_orders[0] : null;
  }

  /**
   * Creates the WooCommerce order
   *
   * @param string $zonosOrderId The Zonos order number
   * @return \WC_Order The order
   * @throws InvalidArgumentException If order failed to be created
   */
  private function createBaseOrder(string $zonosOrderId): \WC_Order
  {
    $wooOrder = wc_create_order(
      [
        'status' => 'wc-processing',
      ]
    );

    if (!$wooOrder) {
      throw new InvalidArgumentException('Failed to create WooCommerce order');
    }

    $wooOrder->update_meta_data('Zonos order number', $zonosOrderId);
    $wooOrder->update_meta_data('order_attribution_origin', 'Zonos Checkout');

    return $wooOrder;
  }

  /**
   * Adds the product to the order
   *
   * @param \WC_Order $wooOrder The order
   * @param Item[] $items The items/products to add
   * @param CartAdjustment[] $adjustments The adjustments to add
   * @throws InvalidArgumentException If the product is not found
   */
  private function addOrderItems(\WC_Order $wooOrder, array $items, array $adjustments): void
  {
    foreach ($items as $item) {
      $product = null;

      if (!empty($item->sku)) {
        $productId = wc_get_product_id_by_sku($item->sku);
        if ($productId) {
          $product = wc_get_product_object($productId);
        }
      }

      if (!$product && !empty($item->productId)) {
        $product = wc_get_product((int)$item->productId);
      }

      if (!$product) {
        throw new InvalidArgumentException("Product not found by SKU: {$item->sku} or ID: {$item->productId}");
      }


      $subtotal = $item->quantity * ($item->amount ?? $product->get_price());
      $total = $subtotal;
      foreach ($adjustments as $adjustment) {
        if ($adjustment->sku !== null && $adjustment->sku !== '' && $adjustment->sku === $item->sku) {
          $total += $adjustment->amount;
        }
      }
      $itemId = $wooOrder->add_product(
        $product,
        $item->quantity,
        [
          'subtotal' => $subtotal,
          'total' => $total,
        ]
      );

      $orderItem = $wooOrder->get_item($itemId);
      foreach ($item->attributes as $attribute) {
        try {
          $taxonomy = wc_attribute_taxonomy_name($attribute->key);
          $attributeName = wc_attribute_label($taxonomy) ?? $attribute->key;
          $attributeValue = get_term_by('slug', $attribute->value, $taxonomy)?->name ?? $attribute->value;
          $orderItem->add_meta_data(str_replace('pa_', '', $attributeName), $attributeValue);
        } catch (\Exception $e) {
          $this->logger->sendLog('Error processing attributes in order creation: '.$e->getMessage());
        }
      }
      $orderItem->save();
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
    $billingParty = $this->findPartyByType($parties, PartyType::PAYOR);
    if ($billingParty !== null) {
      $wooOrder->set_billing_first_name($billingParty->person?->firstName ?? '');
      $wooOrder->set_billing_last_name($billingParty->person?->lastName ?? '');
      $wooOrder->set_billing_company($billingParty->person?->companyName ?? '');
      $wooOrder->set_billing_address_1($billingParty->location?->line1 ?? '');
      $wooOrder->set_billing_address_2($billingParty->location?->line2 ?? '');
      $wooOrder->set_billing_city($billingParty->location?->locality ?? '');
      $wooOrder->set_billing_state($billingParty->location?->administrativeArea ?? '');
      $wooOrder->set_billing_postcode($billingParty->location?->postalCode ?? '');
      $wooOrder->set_billing_country($billingParty->location?->countryCode ?? '');
      $wooOrder->set_billing_email($billingParty->person?->email ?? '');
      $wooOrder->set_billing_phone($billingParty->person?->phone ?? '');
    }

    $shippingParty = $this->findPartyByType($parties, PartyType::DESTINATION);
    if ($shippingParty !== null) {
      $wooOrder->set_shipping_first_name($shippingParty->person?->firstName ?? '');
      $wooOrder->set_shipping_last_name($shippingParty->person?->lastName ?? '');
      $wooOrder->set_shipping_company($shippingParty->person?->companyName ?? '');
      $wooOrder->set_shipping_address_1($shippingParty->location?->line1 ?? '');
      $wooOrder->set_shipping_address_2($shippingParty->location?->line2 ?? '');
      $wooOrder->set_shipping_city($shippingParty->location?->locality ?? '');
      $wooOrder->set_shipping_state($shippingParty->location?->administrativeArea ?? '');
      $wooOrder->set_shipping_postcode($shippingParty->location?->postalCode ?? '');
      $wooOrder->set_shipping_country($shippingParty->location?->countryCode ?? '');
      $wooOrder->set_shipping_phone($shippingParty->person?->phone ?? '');
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
   * @param Order $orderData The order data
   * @param CartAdjustment[] $adjustments The adjustments
   */
  private function processOrderTotals(\WC_Order $wooOrder, Order $orderData, array $adjustments): void
  {
    try {
      if ($orderData->amountSubtotals === null) {
        return;
      }

      $exchangeRate = $this->getExchangeRate($orderData);
      $convertAmount = function (float $amount) use ($exchangeRate): float {
        if ($exchangeRate !== null) {
          return round($amount / $exchangeRate->rate, 2);
        }
        return $amount;
      };

      $discountAmount = $convertAmount($orderData->amountSubtotals->discounts);

      $discountsByDescription = [];
      foreach ($adjustments as $adjustment) {
        $description = $adjustment->description;
        if (!isset($discountsByDescription[$description])) {
          $discountsByDescription[$description] = 0;
        }
        $discountsByDescription[$description] += $adjustment->amount;
      }

      foreach ($discountsByDescription as $description => $amount) {
        $coupon = new \WC_Order_Item_Coupon();
        $coupon->set_code($description);
        $coupon->set_discount(abs($amount));
        $coupon->set_discount_tax(0);
        $wooOrder->add_item($coupon);
      }

      $wooOrder->set_currency($exchangeRate ? $exchangeRate->sourceCurrencyCode : $orderData->currencyCode);

      $this->addShippingIfNeeded($wooOrder, $orderData, $convertAmount);
      $this->addFeeIfNeeded($wooOrder, 'Taxes', $orderData->amountSubtotals->taxes, $convertAmount);
      $this->addFeeIfNeeded($wooOrder, 'Duties', $orderData->amountSubtotals->duties, $convertAmount);
      $this->addFeeIfNeeded($wooOrder, 'Additional Fees', $orderData->amountSubtotals->fees, $convertAmount);

      $wooOrder->calculate_totals(false);

      $wooOrder->set_discount_total(abs($discountAmount));
      $wooOrder->set_discount_tax(0);

      $newTotal = $orderData->amountSubtotals->items + $orderData->amountSubtotals->shipping + $orderData->amountSubtotals->duties + $orderData->amountSubtotals->fees + $orderData->amountSubtotals->taxes + $orderData->amountSubtotals->discounts;
      $wooOrder->set_total($convertAmount($newTotal));
    } catch (\Exception $e) {
      $this->logger->sendLog('Error processing order totals: '.$e->getMessage());
    }
  }

  /**
   * Gets exchange rate if needed
   *
   * @param Order $orderData The order data
   * @return ExchangeRate|null The exchange rate object or null
   */
  private function getExchangeRate(Order $orderData): ?ExchangeRate
  {
    if (
      $orderData->root === null ||
      empty($orderData->root->exchangeRates) ||
      $orderData->currencyCode === reset($orderData->items)->currencyCode ?? ''
    ) {
      return null;
    }

    foreach ($orderData->root->exchangeRates as $rate) {
      if ($rate->targetCurrencyCode === $orderData->currencyCode) {
        return $rate;
      }
    }

    return null;
  }

  /**
   * Adds shipping to the order if needed
   *
   * @param \WC_Order $wooOrder The order
   * @param Order $orderData The order data
   * @param callable $convertAmount The converter
   */
  private function addShippingIfNeeded(\WC_Order $wooOrder, Order $orderData, callable $convertAmount): void
  {
    if ($orderData->amountSubtotals->shipping <= 0) {
      return;
    }

    $shippingMethod = !empty($orderData->shipmentRatings) ? $orderData->shipmentRatings[0] : null;
    $shippingItem = new \WC_Order_Item_Shipping();
    $shippingItem->set_method_title($shippingMethod?->displayName ?? 'Shipping');
    $shippingItem->set_method_id($shippingMethod?->serviceLevelCode ?? 'default');
    $shippingItem->set_total($convertAmount($orderData->amountSubtotals->shipping));
    $wooOrder->add_item($shippingItem);
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
    try {
      if (empty($shipments)) {
        return false;
      }

      $newTrackingNumbers = [];
      foreach ($shipments as $shipment) {
        foreach ($shipment->trackingDetails as $trackingDetail) {
          $newTrackingNumbers[] = wc_clean($trackingDetail->number);
        }
      }

      $existingTrackingString = $wooOrder->get_meta('zonos_tracking_numbers', true);
      $existingTrackingNumbers = !empty($existingTrackingString)
        ? array_map('trim', explode(',', $existingTrackingString))
        : [];

      sort($newTrackingNumbers);
      sort($existingTrackingNumbers);

      if ($newTrackingNumbers !== $existingTrackingNumbers) {
        $wooOrder->update_meta_data('zonos_tracking_numbers', implode(', ', $newTrackingNumbers));

        $addedNumbers = array_diff($newTrackingNumbers, $existingTrackingNumbers);
        if (!empty($addedNumbers)) {
          $wooOrder->add_order_note(
            sprintf(
              'New tracking number(s) added: %s',
              implode(', ', $addedNumbers)
            ),
            false
          );
        }
        return true;
      }

    } catch (\Exception $e) {
      $this->logger->sendLog('Error updating order tracking numbers: '.$e->getMessage());
    }
    return false;
  }

  /**
   * Maps Zonos status to WooCommerce status
   *
   * @param OrderStatus $zonosStatus The Zonos order status
   * @return string The WooCommerce status
   */
  private function mapOrderStatus(OrderStatus $zonosStatus): string
  {
    return match ($zonosStatus) {
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
   * @param OrderStatus $newStatus The new status from Zonos
   * @return bool Whether the status was updated
   */
  private function setOrderStatus(\WC_Order $wooOrder, OrderStatus $newStatus): bool
  {
    $newWcStatus = $this->mapOrderStatus($newStatus);
    $currentStatus = 'wc-' . $wooOrder->get_status();

    if ($currentStatus !== $newWcStatus) {
      $wooOrder->set_status($newWcStatus, '', true);
      return true;
    }

    return false;
  }
}

