<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class AmountSubtotals
{
  public function __construct(
    public float $discounts,
    public float $duties,
    public float $fees,
    public float $items,
    public float $shipping,
    public float $taxes,
  ) {
  }

  public function toArray(): array
  {
    return [
      'discounts' => $this->discounts,
      'duties' => $this->duties,
      'fees' => $this->fees,
      'items' => $this->items,
      'shipping' => $this->shipping,
      'taxes' => $this->taxes,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      (float)($data['discounts'] ?? 0.0),
      (float)($data['duties'] ?? 0.0),
      (float)($data['fees'] ?? 0.0),
      (float)($data['items'] ?? 0.0),
      (float)($data['shipping'] ?? 0.0),
      (float)($data['taxes'] ?? 0.0),
    );
  }
}