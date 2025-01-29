<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class CheckoutSession
{
  public function __construct(
    public ?string $cartId,
  ) {
  }

  public function toArray(): array
  {
    return [
      'cartId' => $this->cartId,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      cartId: $data['cartId'] ?? null,
    );
  }
}
