<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Cart
{
  public function __construct(
    /** @var CartAdjustment[]|null $adjustments */
    public ?array  $adjustments,
    public ?string $id,
  ) {
  }

  public function toArray(): array
  {
    return [
      'adjustments' => $this->adjustments ? array_map(fn(CartAdjustment $adjustment) => $adjustment->toArray(), $this->adjustments) : null,
      'id' => $this->id,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      adjustments: isset($data['adjustments']) ? array_map(fn(array $adjustment) => CartAdjustment::fromArray($adjustment), $data['adjustments']) : null,
      id:          $data['id'] ?? null,
    );
  }
}
