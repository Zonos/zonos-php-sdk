<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class PluginCredential
{
  public function __construct(
    public string          $id,
    public ?string         $type = '',
    public ?string         $mode = '',
  ) {
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'mode' => $this->mode,
      'type' => $this->type,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      id:           $data['id'] ?? '',
      mode:         $data['mode'] ?? '',
      type:         $data['type'] ?? '',
    );
  }
}
