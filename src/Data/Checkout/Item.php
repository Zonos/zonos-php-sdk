<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Attribute;

class Item
{

  public function __construct(
    public float  $amount,
    public array  $attributes,
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
      'attributes' => array_map(fn(Attribute $attribute) => $attribute->toArray(), $this->attributes),
      'currencyCode' => $this->currencyCode,
      'productId' => $this->productId,
      'quantity' => $this->quantity,
      'sku' => $this->sku,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      amount:       (float)($data['amount']) ?? 0.0,
      attributes:   array_map(
                      fn(array $attribute) => Attribute::fromArray($attribute),
                      $data['attributes'] ?? []
                    ) ?? [],
      currencyCode: $data['currencyCode'] ?? '',
      productId:    $data['productId'] ?? '',
      quantity:     $data['quantity'] ?? 0,
      sku:          $data['sku'] ?? '',
    );
  }
}
