<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Order
{
  public string $id;

  public function __construct(
    string $id,
  ) {
    $this->id = $id;
  }


  public function toArray(): array
  {
    return [
      'id' => $this->id,
    ];
  }

  public static function fromArray(array $data): self
  {
    $id = $data['id'] ?? '';

    return new self(
      $id,
    );
  }
}
