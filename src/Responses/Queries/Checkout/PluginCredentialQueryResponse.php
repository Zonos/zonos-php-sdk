<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\PluginCredential;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class PluginCredentialQueryResponse extends ZonosResponse
{
  public function resolve(): ?PluginCredential
  {
    $pluginCredential = $this->json('data.pluginCredential');
    if ($pluginCredential === null) {
      return null;
    }
    return PluginCredential::fromArray($pluginCredential);
  }
}

