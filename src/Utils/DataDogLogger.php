<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Utils;

use Zonos\ZonosSdk\Connectors\Logger\DataDogLoggerConnector;
use Zonos\ZonosSdk\Requests\Logger\DataDogLoggerRequest;

class DataDogLogger
{
  /**
   * Create a new DataDogLogger instance
   *
   * @param string $credentialToken Authentication token for API access
   */
  public function __construct(
    protected string $credentialToken,
  ) {
  }

  public function sendLog(string $message, string $service)
  {
    $connector = new DataDogLoggerConnector(
      credentialToken: $this->credentialToken,
      baseUrl:         'https://plugins.zonos.com'
    );

    $request = new DataDogLoggerRequest(
      [
        "platform" => "php-zonos-sdk",
        "message" => $message,
      ]
    );
    $connector->send($request);
  }
}
