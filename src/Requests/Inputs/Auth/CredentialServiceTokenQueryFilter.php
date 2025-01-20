<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

use Zonos\ZonosSdk\Data\Auth\Enums\CredentialType;
use Zonos\ZonosSdk\Data\Auth\Enums\Mode;

class CredentialServiceTokenQueryFilter
{
  public function __construct(
    public ?Mode           $mode = null,
    public ?string         $organizationId = null,
    public ?int            $storeId = null,
    public ?CredentialType $type = null,
  ) {
  }

  public function toArray(): array
  {
    return [
      'mode' => $this->mode?->value,
      'organizationId' => $this->organizationId,
      'storeId' => $this->storeId,
      'type' => $this->type?->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      mode:           isset($data['mode']) ? Mode::from($data['mode']) : null,
      organizationId: $data['organizationId'] ?? null,
      storeId:        $data['storeId'] ?? null,
      type:           isset($data['type']) ? CredentialType::from($data['type']) : null,
    );
  }
}
