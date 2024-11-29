<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses;

use Saloon\Http\Response;

/**
 * Abstract class for Zonos responses
 */
abstract class ZonosResponse extends Response
{
  /**
   * Resolve the response
   *
   * @return mixed
   */
  abstract public function resolve();
}
