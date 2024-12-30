<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

class OrderUpdateAccountOrderNumberInput
{
  public function __construct(
    public string $accountOrderNumber,
    public string $id,
  ) {
  }

  public function toArray(): array
  {
    return [
      'accountOrderNumber' => $this->accountOrderNumber,
      'id' => $this->id,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      accountOrderNumber: $data['accountOrderNumber'] ?? '',
      id:                 $data['id'] ?? ''
    );
  }
}