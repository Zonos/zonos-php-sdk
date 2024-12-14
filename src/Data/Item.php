<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Item
{

  public function __construct(
    public string $sku,
    public string $productId,
    public int    $quantity,
  ) {
  }


  public function toArray(): array
  {
    return [
      'sku' => $this->sku,
      'productId' => $this->productId,
      'quantity' => $this->quantity,
    ];
  }

  public static function fromArray(array $data): self
  {
    $sku = $data['sku'] ?? '';
    $productId = $data['productId'] ?? '';
    $quantity = $data['quantity'] ?? 0;
    return new self(
      $sku,
      $productId,
      $quantity
    );
  }
}
