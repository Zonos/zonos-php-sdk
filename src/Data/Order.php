<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data;

class Order
{

  public function __construct(
    public ?AmountSubtotals $amountSubtotals,
    public string           $currencyCode,
    public string           $id,
    /** @var Item[] $items */
    public array            $items,
    /** @var Party[] $parties */
    public array            $parties,
    /** @var ShipmentRating[] $shipmentRatings */
    public array            $shipmentRatings,
    public string           $status,
  ) {
  }


  public function toArray(): array
  {
    return [
      'amountSubtotals' => $this->amountSubtotals?->toArray(),
      'currencyCode' => $this->currencyCode,
      'id' => $this->id,
      'items' => array_map(fn(Item $item) => $item->toArray(), $this->items),
      'parties' => array_map(fn(Party $party) => $party->toArray(), $this->parties),
      'shipmentRatings' => array_map(fn(ShipmentRating $shipmentRating) => $shipmentRating->toArray(), $this->shipmentRatings),
      'status' => $this->status
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      isset($data['amountSubtotals']) ? AmountSubtotals::fromArray($data['amountSubtotals']) : null,
      $data['currencyCode'] ?? '',
      $data['id'] ?? '',
      array_map(
        fn(array $item) => Item::fromArray($item),
        $data['items'] ?? []
      ) ?? [],
      array_map(
        fn(array $party) => Party::fromArray($party),
        $data['parties'] ?? []
      ) ?? [],
      array_map(
        fn(array $shipmentRating) => Party::fromArray($shipmentRating),
        $data['shipmentRatings'] ?? []
      ) ?? [],
      $data['status'] ?? '',
    );
  }
}
