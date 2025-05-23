<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Location
{
  public function __construct(
    public string $administrativeArea,
    public string $countryCode,
    public string $line1,
    public string $line2,
    public string $locality,
    public string $postalCode,
  ) {
  }

  public function toArray(): array
  {
    return [
      'administrativeArea' => $this->administrativeArea,
      'countryCode' => $this->countryCode,
      'line1' => $this->line1,
      'line2' => $this->line2,
      'locality' => $this->locality,
      'postalCode' => $this->postalCode,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      administrativeArea: $data['administrativeArea'] ?? '',
      countryCode:        $data['countryCode'] ?? '',
      line1:              $data['line1'] ?? '',
      line2:              $data['line2'] ?? '',
      locality:           $data['locality'] ?? '',
      postalCode:         $data['postalCode'] ?? '',
    );
  }
}