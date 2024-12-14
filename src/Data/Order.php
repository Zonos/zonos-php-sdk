<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Order
{

  public function __construct(
    public string $id,
    public array  $items = []
  ) {
  }


  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'items' => array_map(fn(Item $item) => $item->toArray(), $this->items),
    ];
  }

  public static function fromArray(array $data): self
  {
    $id = $data['id'] ?? '';
    $items = array_map(
      fn(array $item) => Item::fromArray($item),
      $data['items'] ?? []
    );

    return new self(
      $id,
      $items
    );
  }
}
