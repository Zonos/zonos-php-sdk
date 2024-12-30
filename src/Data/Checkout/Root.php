<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Root
{
  public function __construct(
    /** @var ExchangeRate[] $exchangeRates */
    public array $exchangeRates,
  ) {
  }

  public function toArray(): array
  {
    return [
      'exchangeRates' => array_map(fn(ExchangeRate $exchangeRate) => $exchangeRate->toArray(), $this->exchangeRates),
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      exchangeRates: array_map(
                       fn(array $exchangeRate) => ExchangeRate::fromArray($exchangeRate),
                       $data['exchangeRates'] ?? []
                     ) ?? [],
    );
  }
}