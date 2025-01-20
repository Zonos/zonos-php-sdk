<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Auth;

use Zonos\ZonosSdk\Data\Auth\Enums\CredentialType;

class Credential
{
  public function __construct(
    public string          $organization,
    public string          $id,
    public ?CredentialType $type = null,
  ) {
  }

  public function toArray(): array
  {
    return [
      'organization' => $this->organization,
      'id' => $this->id,
      'type' => $this->type?->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      organization: $data['organization'] ?? '',
      id:           $data['id'] ?? '',
      type:         isset($data['type']) ? CredentialType::from($data['type']) : null,
    );
  }
}
