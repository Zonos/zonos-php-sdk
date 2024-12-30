<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Attribute
{
  public function __construct(
    public string $key,
    public string $value,
  ) {
  }

  public function toArray(): array
  {
    return [
      'key' => $this->key,
      'value' => $this->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      key:   $data['key'],
      value: $data['value'],
    );
  }
}