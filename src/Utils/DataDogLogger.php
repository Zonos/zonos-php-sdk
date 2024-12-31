<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Utils;

use Zonos\ZonosSdk\Connectors\Logger\DataDogLoggerConnector;
use Zonos\ZonosSdk\Requests\Logger\DataDogLoggerRequest;

class DataDogLogger
{
  public function sendLog(string $message, string $service)
  {
    $connector = new DataDogLoggerConnector('http://host.docker.internal:3000'); // TODO: review this url
    $request = new DataDogLoggerRequest([
      "ddsource" => "woocommerce plugin",
      "ddtags" => "",
      "hostname" => "i-012345678",
      "message" => $message,
      "service" => $service
    ]);
    $connector->send($request);
  }
}
