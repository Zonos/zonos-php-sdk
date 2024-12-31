<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Logger;

use Saloon\Http\Connector;

class DataDogLoggerConnector extends Connector
{
  public function __construct(
    protected string $base_url,
  ){}

  /**
   * Resolve the base URL
   *
   * @return string
   */
  public function resolveBaseUrl(): string
  {
    return $this->base_url;
  }

  // Optional: Add default headers or authentication methods
  public function defaultHeaders(): array
  {
    return [
      'Accept' => 'application/json',
    ];
  }
}