<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Data\Order;

/**
 * Abstract class for Zonos services
 */
abstract class AbstractZonosService
{
  /**
   * Constructor for the AbstractZonosService
   *
   * @param ZonosConnector $connector The connector instance
   */
  public function __construct(
    protected readonly ZonosConnector    $connector,
    protected readonly DataMapperService $data_mapper_service,
  ) {
  }

  /**
   * Store an order in the database
   *
   * @param array $order_data Order data with required fields
   * @return int The ID of the created order
   */
  abstract public function storeOrder(Order $order_data): \WC_Order;
}
