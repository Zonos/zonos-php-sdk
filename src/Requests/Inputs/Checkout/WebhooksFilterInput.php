<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookStatus;
use Zonos\ZonosSdk\Data\Checkout\Enums\WebhookType;

class WebhooksFilterInput
{
  public function __construct(
    public ?WebhookStatus $status = null,
    public ?WebhookType   $type = null,
  ) {
  }

  public function toArray(): array
  {
    return [
      'status' => $this->status?->value,
      'type' => $this->type?->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      status: isset($data['status']) ? WebhookStatus::from($data['status']) : null,
      type:   isset($data['type']) ? WebhookType::from($data['type']) : null,
    );
  }
}

