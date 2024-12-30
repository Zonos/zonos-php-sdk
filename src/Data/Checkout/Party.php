<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\PartyType;

class Party
{
  public function __construct(
    public ?Location $location,
    public ?Person   $person,
    public PartyType $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'location' => $this->location?->toArray(),
      'person' => $this->person?->toArray(),
      'type' => $this->type->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      location: isset($data['location']) ? Location::fromArray($data['location']) : null,
      person:   isset($data['person']) ? Person::fromArray($data['person']) : null,
      type:     PartyType::from($data['type'] ?? PartyType::ORIGIN->value),
    );
  }
}
