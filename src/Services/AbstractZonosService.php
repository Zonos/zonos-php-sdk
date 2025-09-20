<?php

declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\Cart;
use Zonos\ZonosSdk\Data\Checkout\Order;
use Zonos\ZonosSdk\Utils\DataDogLogger;

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
   * @param DataDogLogger $logger Service to send logs into datadog
   */
  public function __construct(
    protected readonly ZonosConnector    $connector,
    protected readonly DataMapperService $dataMapperService,
    protected readonly DataDogLogger     $logger,
  ) {}

  /**
   * Store an order in the e-commerce platform
   *
   * @param Order $orderData The Zonos order data to store
   * @param Cart $cartData The Zonos cart data to store
   * @param string $storeCurrencyCode The store currency code
   * @return mixed The created order in the platform's format
   */
  abstract public function storeOrder(Order $orderData, Cart $cartData, string $storeCurrencyCode): mixed;


  /**
   * Update an existing order in the e-commerce platform
   *
   * @param Order $orderData
   * @return mixed The updated order in the platform's format
   */
  abstract public function updateOrder(Order $orderData): mixed;

  /**
   * Export an order from the e-commerce platform in Zonos format
   *
   * @return string|null The order data in Zonos format
   */
  abstract public function exportOrder(): ?string;

  abstract public function getCredentialToken(string $type, string $mode): string;
}
