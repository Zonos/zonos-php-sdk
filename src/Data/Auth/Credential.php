<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Auth;

class Credential
{
  public function __construct(
    public string $organization,
    public string $id,
    public string $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'organization' => $this->organization,
      'id' => $this->id,
      'type' => $this->type,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['organization'] ?? '',
      $data['id'] ?? '',
      $data['type'] ?? '',
    );
  }
}