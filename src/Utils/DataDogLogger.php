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
   * @param array $clientHeaders Client headers
   */
  public function __construct(
    protected string $credentialToken,
    protected array  $clientHeaders,
  ) {
  }

  /**
   * Sends a log message to the DataDog logging service.
   *
   * @param string $message The log message to be sent.
   * @return void
   */
  public function sendLog(string $message): void
  {
    $connector = new DataDogLoggerConnector(
      credentialToken: $this->credentialToken,
      baseUrl:         'https://plugins.zonos.com',
      clientHeaders:   $this->clientHeaders,
    );

    $request = new DataDogLoggerRequest(
      [
        "platform" => "WordpressCheckout",
        "message" => $message,
      ]
    );

    $connector->send($request);
  }
}
