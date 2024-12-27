<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\PartyType;

class ShipmentRating
{
  public function __construct(
    public string $displayName,
    public string $serviceLevelCode,
  ) {
  }

  public function toArray(): array
  {
    return [
      'displayName' => $this->displayName,
      'serviceLevelCode' => $this->serviceLevelCode,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['displayName'] ?? '',
      $data['serviceLevelCode'] ?? '',
    );
  }
}
