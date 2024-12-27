<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Item
{

  public function __construct(
    public float  $amount,
    public string $currencyCode,
    public string $productId,
    public int    $quantity,
    public string $sku,
  ) {
  }


  public function toArray(): array
  {
    return [
      'amount' => $this->amount,
      'currencyCode' => $this->currencyCode,
      'productId' => $this->productId,
      'quantity' => $this->quantity,
      'sku' => $this->sku,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      (float)($data['amount']) ?? 0.0,
      $data['currencyCode'] ?? '',
      $data['productId'] ?? '',
      $data['quantity'] ?? 0,
      $data['sku'] ?? '',
    );
  }
}
