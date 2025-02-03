<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

class CartCreateInput
{
  /**
   * @var CartAdjustmentInput[]|null $adjustments
   * @var ItemInput[] $items
   * @var CartMetadataInput[]|null $metadata
   **/
  public function __construct(
    public ?array $adjustments,
    public array  $items,
    public ?array $metadata,
  ) {
  }

  public function toArray(): array
  {
    return [
      'adjustments' => $this->adjustments,
      'items' => $this->items,
      'metadata' => $this->metadata,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      adjustments: isset($data['adjustments']) ? array_map(
                   fn(array $adjustment) => CartAdjustmentInput::fromArray($adjustment),
                   $data['adjustments'] ?? []
                 ) : null,
      items:       array_map(
                     fn(array $item) => ItemInput::fromArray($item),
                     $data['items'] ?? []
                   ) ?? [],
      metadata:    isset($data['metadata']) ? array_map(
                     fn(array $metadata) => CartMetadataInput::fromArray($metadata),
                     $data['metadata'] ?? []
                   ) : null,
    );
  }
}
