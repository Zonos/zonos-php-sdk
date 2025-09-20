<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\OrderStatus;

class Order
{

  public function __construct(
    public string                   $accountOrderNumber,
    public ?AmountSubtotals         $amountSubtotals,
    /** @var AmountSubtotalsDetails[] $amountSubtotalsDetails */
    public array                    $amountSubtotalsDetails,
    public ?CheckoutSession         $checkoutSession,
    public string                   $currencyCode,
    public string                   $id,
    /** @var Item[] $items */
    public array                    $items,
    /** @var Party[] $parties */
    public array                    $parties,
    public ?Root                    $root,
    /** @var ShipmentRating[] $shipmentRatings */
    public array                    $shipmentRatings,
    public OrderStatus              $status,
    /** @var Shipment[] $shipments */
    public array                    $shipments,
    public ?string                  $zonosOrderId,
  ) {
  }


  public function toArray(): array
  {
    return [
      'accountOrderNumber' => $this->accountOrderNumber,
      'amountSubtotals' => $this->amountSubtotals?->toArray(),
      'amountSubtotalsDetails' => array_map(fn(AmountSubtotalsDetails $amountSubtotalsDetails) => $amountSubtotalsDetails->toArray(), $this->amountSubtotalsDetails),
      'checkoutSession' => $this->checkoutSession?->toArray(),
      'currencyCode' => $this->currencyCode,
      'id' => $this->id,
      'items' => array_map(fn(Item $item) => $item->toArray(), $this->items),
      'parties' => array_map(fn(Party $party) => $party->toArray(), $this->parties),
      'root' => $this->root?->toArray(),
      'shipmentRatings' => array_map(fn(ShipmentRating $shipmentRating) => $shipmentRating->toArray(), $this->shipmentRatings),
      'status' => $this->status->value,
      'shipments' => array_map(fn(Shipment $shipment) => $shipment->toArray(), $this->shipments),
      'zonosOrderId' => $this->zonosOrderId,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      accountOrderNumber:     $data['accountOrderNumber'] ?? '',
      amountSubtotals:        isset($data['amountSubtotals']) ? AmountSubtotals::fromArray($data['amountSubtotals']) : null,
      amountSubtotalsDetails: array_map(
                                fn(array $amountSubtotalsDetails) => AmountSubtotalsDetails::fromArray($amountSubtotalsDetails),
                            $data['amountSubtotalsDetails'] ?? []
                              ) ?? [],
      checkoutSession:        isset($data['checkoutSession']) ? CheckoutSession::fromArray($data['checkoutSession']) : null,
      currencyCode:           $data['currencyCode'] ?? '',
      id:                     $data['id'] ?? '',
      items:                  array_map(
                                fn(array $item) => Item::fromArray($item),
                            $data['items'] ?? []
                              ) ?? [],
      parties:                array_map(
                                fn(array $party) => Party::fromArray($party),
                            $data['parties'] ?? []
                              ) ?? [],
      root:                   isset($data['root']) ? Root::fromArray($data['root']) : null,
      shipmentRatings:        array_map(
                                fn(array $shipmentRating) => ShipmentRating::fromArray($shipmentRating),
                            $data['shipmentRatings'] ?? []
                              ) ?? [],
      status:                 OrderStatus::from($data['status'] ?? OrderStatus::OPEN->value),
      shipments:              array_map(
                                fn(array $shipment) => Shipment::fromArray($shipment),
                                $data['shipments'] ?? []
                              ) ?? [],
      zonosOrderId:           $data['zonosOrderId'] ?? null,
    );
  }
}
