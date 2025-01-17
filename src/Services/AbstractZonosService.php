<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Order;

/**
 * Abstract base class for Zonos services
 *
 * Provides common functionality and contract for services that handle
 * Zonos order processing and data mapping.
 */
abstract class AbstractZonosService
{
  /**
   * Constructor for the AbstractZonosService
   *
   * @param ZonosConnector $connector The Zonos API connector instance
   * @param DataMapperService $dataMapperService Service for mapping data between systems
   */
  public function __construct(
    protected readonly ZonosConnector    $connector,
    protected readonly DataMapperService $dataMapperService,
  ) {
  }

  /**
   * Store an order in the e-commerce platform
   *
   * @param Order $orderData The Zonos order data to store
   * @return mixed The created order in the platform's format
   */
  abstract public function storeOrder(Order $orderData): mixed;


  /**
   * Update an existing order in the e-commerce platform
   *
   * @param Order $orderData
   * @return mixed The updated order in the platform's format
   */
  abstract public function updateOrder(Order $orderData): mixed;

  /**
   * Add tracking numbers to an order
   *
   * @param string $orderId The order ID
   * @param string $trackingNumber The tracking number
   * @return bool Whether the tracking number was added
   */
  abstract public function addTrackingNumberToOrder(string $orderId, string $trackingNumber): bool;

  /**
   * Update the status of an order
   *
   * @param string $orderId The order ID
   * @param string $status The new status
   * @return bool Whether the status was updated
   */
  abstract public function updateOrderStatus(string $orderId, string $status): bool;

  /**
   * Export an order from the e-commerce platform in Zonos format
   *
   * @return array<string, mixed> The order data in Zonos format
   */
  abstract public function exportOrder(): array;
}
