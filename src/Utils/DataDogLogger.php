<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Utils;

use Zonos\ZonosSdk\Connectors\Logger\DataDogLoggerConnector;
use Zonos\ZonosSdk\Requests\Logger\DataDogLoggerRequest;

class DataDogLogger
{
  public function sendLog(string $message, string $service)
  {
    $connector = new DataDogLoggerConnector('https://http-intake.logs.datadoghq.com');
    // TODO: Instead of sending the data from datadog we want to update to zonos messages  https://plugins.zonos.com/sendToZonos
    // Header service token (user input)

    /*
     *
     *  HEADERS
     *  user-token: {IGuser token form dashboard}
     *  or
     *  service-token: {zonos api key}
     *  BODY
     *  message: The text of the Slack message.
     *  platform: The platform to which the message will be sent. If the platform field is not recognized, the message will be sent to the default channel.
     */
    $request = new DataDogLoggerRequest(
      [
        "ddsource" => "php-zonos-sdk",
        "ddtags" => "sdk_version:v1.0.0",
        "hostname" => gethostname(),
        "message" => $message,
        "service" => $service
      ]
    );
    $connector->send($request);
  }
}
