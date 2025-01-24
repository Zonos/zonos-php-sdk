<?php declare(strict_types=1);


namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\ItemMeasurementType;
use Zonos\ZonosSdk\Data\Checkout\Enums\ItemUnitOfMeasure;

class ItemMeasurementInput
{
  public function __construct(
    public ItemMeasurementType $type,
    public ItemUnitOfMeasure   $unitOfMeasure,
    public float               $value,
  ) {
  }

  public function toArray(): array
  {
    return [
      'type' => $this->type->value,
      'unitOfMeasure' => $this->unitOfMeasure->value,
      'value' => $this->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      type:          ItemMeasurementType::from($data['type']),
      unitOfMeasure: ItemUnitOfMeasure::from($data['unitOfMeasure']),
      value:         (float)$data['value'] ?? 0.0,
    );
  }
}
