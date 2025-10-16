<?php

declare(strict_types=1);


namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\ItemMeasurementType;
use Zonos\ZonosSdk\Data\Checkout\Enums\ItemUnitOfMeasure;

class ItemMeasurementInput
{
  public function __construct(
    public ItemMeasurementType $type,
    public ItemUnitOfMeasure   $unitOfMeasure,
    public float               $value,
  ) {}

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
    if (!isset($data['type'])) {
      throw new \RuntimeException('Missing required field: type');
    }
    if (!isset($data['unitOfMeasure'])) {
      throw new \RuntimeException('Missing required field: unitOfMeasure');
    }

    try {
      $type = ItemMeasurementType::from($data['type']);
    } catch (\ValueError $e) {
      throw new \RuntimeException('Invalid ItemMeasurementType: "' . $data['type'] . '". Valid values are: ' . implode(', ', array_column(ItemMeasurementType::cases(), 'value')));
    } catch (\Exception $e) {
      throw new \RuntimeException('Error creating ItemMeasurementType from: "' . $data['type'] . '" - ' . $e->getMessage(), 0, $e);
    }

    try {
      $unitOfMeasure = ItemUnitOfMeasure::from($data['unitOfMeasure']);
    } catch (\ValueError $e) {
      throw new \RuntimeException('Invalid ItemUnitOfMeasure: "' . $data['unitOfMeasure'] . '". Valid values are: ' . implode(', ', array_column(ItemUnitOfMeasure::cases(), 'value')));
    } catch (\Exception $e) {
      throw new \RuntimeException('Error creating ItemUnitOfMeasure from: "' . $data['unitOfMeasure'] . '" - ' . $e->getMessage(), 0, $e);
    }

    return new self(
      type: $type,
      unitOfMeasure: $unitOfMeasure,
      value: (float)($data['value'] ?? 0.0),
    );
  }
}
