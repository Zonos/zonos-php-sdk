<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses\Queries\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Webhook;
use Zonos\ZonosSdk\Data\Misc\Paginated;
use Zonos\ZonosSdk\Responses\ZonosResponse;

class WebhooksQueryResponse extends ZonosResponse
{
  /**
   * @return Paginated<Webhook>|null
   */
  public function resolve(): ?Paginated
  {
    /** @var array{data?: array{webhooks?: array{edges: array<array{cursor: string, node: array}>, totalCount: int}}}|null $webhookData */
    $webhookData = $this->json('data.webhooks');
    if ($webhookData === null) {
      return null;
    }
    return Paginated::fromArray($webhookData, [Webhook::class, 'fromArray']);
  }
} 
