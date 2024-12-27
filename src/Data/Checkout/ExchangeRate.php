<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class ExchangeRate
{
  public function __construct(
    public float  $rate,
    public string $targetCurrencyCode,
    public string $sourceCurrencyCode,
  ) {
  }

  public function toArray(): array
  {
    return [
      'rate' => $this->rate,
      'targetCurrencyCode' => $this->targetCurrencyCode,
      'sourceCurrencyCode' => $this->sourceCurrencyCode,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      (float)($data['rate']) ?? 0.0,
      $data['targetCurrencyCode'] ?? '',
      $data['sourceCurrencyCode'] ?? '',
    );
  }
}