<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Shipment
{
  public function __construct(
    /** @var TrackingDetails[] $trackingDetails */
    public array $trackingDetails,
  ) {
  }

  public function toArray(): array
  {
    return [
      'trackingDetails' => array_map(fn(TrackingDetails $trackingDetail) => $trackingDetail->toArray(), $this->trackingDetails),
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      trackingDetails: array_map(
                         fn(array $trackingDetail) => TrackingDetails::fromArray($trackingDetail),
                         $data['trackingDetails'] ?? []
                       ) ?? [],
    );
  }
}