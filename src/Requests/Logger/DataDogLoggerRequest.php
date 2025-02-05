<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Logger;

use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/*
 * DataDogLoggerRequest class for handling Zonos Logging API requests
 */

class DataDogLoggerRequest extends Request implements HasBody
{
  use HasJsonBody;

  protected Method $method = Method::POST;

  /**
   * Constructor for the DataDogLoggerRequest
   *
   * @param string|null $response The response type
   */
  public function __construct(
    protected array $logBody,
  ) {
  }

  /**
   * Resolve the endpoint for the request
   *
   * @return string
   */
  public function resolveEndpoint(): string
  {
    return '/sendToZonos';
  }

  /**
   * Default body for the request
   *
   * @return array
   */
  protected function defaultBody(): array
  {
    return $this->logBody;
  }
}
