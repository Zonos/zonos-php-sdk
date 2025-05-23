<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class TrackingDetails
{
  public function __construct(
    public string $number,
  ) {
  }

  public function toArray(): array
  {
    return [
      'number' => $this->number,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      number: $data['number'] ?? '',
    );
  }
}