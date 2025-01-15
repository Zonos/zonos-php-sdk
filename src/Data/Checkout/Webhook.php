<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\Mode;
use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookStatus;
use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookType;

class Webhook
{
  public function __construct(
    public string        $id,
    public Mode          $mode,
    public WebhookStatus $status,
    public WebhookType   $type,
    public string        $url,
  ) {
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'mode' => $this->mode->value,
      'status' => $this->status->value,
      'type' => $this->type->value,
      'url' => $this->url,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      id:     $data['id'] ?? '',
      mode:   Mode::from($data['mode'] ?? Mode::LIVE->value),
      status: WebhookStatus::from($data['status'] ?? WebhookStatus::ENABLED->value),
      type:   WebhookType::from($data['type'] ?? WebhookType::ORDER_CREATED->value),
      url:    $data['url'] ?? '',
    );
  }
}
