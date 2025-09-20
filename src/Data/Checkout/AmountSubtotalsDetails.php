<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class AmountSubtotalsDetails
{
  public function __construct(
    public float  $amount,
    public string $currencyCode,
    public string $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'amount' => $this->amount,
      'currencyCode' => $this->currencyCode,
      'type' => $this->type,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      amount:       (float)($data['amount'] ?? 0.0),
      currencyCode: (string)($data['currencyCode'] ?? ''),
      type:         (string)($data['type'] ?? ''),
    );
  }
}