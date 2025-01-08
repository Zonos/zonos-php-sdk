<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Logger;

use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class DataDogLoggerRequest extends Request implements HasBody
{
  use HasJsonBody;

  // Define the HTTP method
  protected Method $method = Method::POST;

  public function __construct(
    protected array $log_body,
  ) {
  }

  // Define the endpoint
  public function resolveEndpoint(): string
  {
    return '/api/v2/logs';
  }

  // Define the data to send with the request
  protected function defaultBody(): array
  {
    return $this->log_body;
  }
}
