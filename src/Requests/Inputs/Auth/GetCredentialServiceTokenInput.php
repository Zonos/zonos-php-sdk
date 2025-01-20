<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

use Zonos\ZonosSdk\Data\Auth\Enums\Mode;

class GetCredentialServiceTokenInput
{
  public function __construct(
    public ?int  $storeId,
    public ?Mode $mode,
  ) {
  }

  public function toArray(): array
  {
    return [
      'storeId' => $this->storeId,
      'mode' => $this->mode?->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      storeId: $data['storeId'] ?? null,
      mode:    isset($data['mode']) ? Mode::from($data['mode']) : null,
    );
  }
}
