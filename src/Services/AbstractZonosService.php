<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Services;

use Zonos\ZonosSdk\Connectors\ZonosConnector;

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
    protected readonly DataMapperService $dataMapper,
  ) {
  }

  /**
   * Store an order in the database
   *
   * @param array $orderData Order data with required fields
   * @return int The ID of the created order
   */
  abstract public function storeOrder(array $orderData): int;
}
