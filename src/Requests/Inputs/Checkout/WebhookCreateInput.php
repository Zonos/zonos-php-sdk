<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookStatus;
use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookType;

class WebhookCreateInput
{
  public function __construct(
    public WebhookStatus $status,
    public WebhookType   $type,
    public string        $url,
  ) {
  }

  public function toArray(): array
  {
    return [
      'status' => $this->status->value,
      'type' => $this->type->value,
      'url' => $this->url,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      status: WebhookStatus::from($data['status'] ?? WebhookStatus::ENABLED->value),
      type:   WebhookType::from($data['type'] ?? WebhookType::ORDER_CREATED->value),
      url:    $data['url'],
    );
  }
}
