<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

class GetCredentialServiceTokenInput
{
  public function __construct(
    public int    $storeId,
    public string $mode,
  ) {
  }

  public function toArray(): array
  {
    return [
      'storeId' => $this->storeId,
      'mode' => $this->mode,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      storeId: $data['storeId'] ?? 0,
      mode:    $data['mode'] ?? ''
    );
  }
}