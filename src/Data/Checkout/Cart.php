<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Cart
{
  public function __construct(
    public ?string $id,
  ) {
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      id: $data['id'] ?? null,
    );
  }
}
