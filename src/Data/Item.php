<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Item
{

  public function __construct(
    public float  $amount,
    public string $productId,
    public int    $quantity,
    public string $sku,
  ) {
  }


  public function toArray(): array
  {
    return [
      'amount' => $this->amount,
      'productId' => $this->productId,
      'quantity' => $this->quantity,
      'sku' => $this->sku,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      (float)($data['amount']) ?? 0.0,
      $data['productId'] ?? '',
      $data['quantity'] ?? 0,
      $data['sku'] ?? '',
    );
  }
}
