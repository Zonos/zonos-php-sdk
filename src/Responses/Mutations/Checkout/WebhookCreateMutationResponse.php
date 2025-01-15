<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Mutations\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Webhook;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class WebhookCreateMutationResponse extends ZonosResponse
{
  public function resolve(): ?Webhook
  {
    $webhookData = $this->json('data.webhookCreate');
    if ($webhookData === null) {
      return null;
    }
    return Webhook::fromArray($webhookData);
  }
}