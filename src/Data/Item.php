<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Item
{

  public function __construct(
    public string $productId,
    public int    $quantity,
    public string $sku, // TODO: wc_get_product uses productId however i might want to use this if there is failure
  )
  {
  }


  public function toArray(): array
  {
    return [
      'productId' => $this->productId,
      'quantity' => $this->quantity,
      'sku' => $this->sku,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['productId'] ?? '',
      $data['quantity'] ?? 0,
      $data['sku'] ?? '',
    );
  }
}
