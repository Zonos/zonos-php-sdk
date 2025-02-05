<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\CartAdjustmentType;
use Zonos\ZonosSdk\Data\Checkout\Enums\CurrencyCode;

class CartAdjustmentInput
{
  public function __construct(
    public float              $amount,
    public CurrencyCode       $currencyCode,
    public ?string            $description,
    public ?string            $productId,
    public ?string            $sku,
    public CartAdjustmentType $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'amount' => $this->amount,
      'currencyCode' => $this->currencyCode->value,
      'description' => $this->description,
      'productId' => $this->productId,
      'sku' => $this->sku,
      'type' => $this->type->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      amount:       $data['amount'],
      currencyCode: CurrencyCode::from($data['currencyCode']),
      description:  $data['description'] ?? null,
      productId:    $data['productId'] ?? null,
      sku:          $data['sku'] ?? null,
      type:         CartAdjustmentType::from($data['type']),
    );
  }
}
