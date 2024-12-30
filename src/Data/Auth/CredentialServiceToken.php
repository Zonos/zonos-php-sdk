<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Auth;

class CredentialServiceToken
{
  public function __construct(
    public float       $storeId,
    public ?Credential $credential,
  ) {
  }

  public function toArray(): array
  {
    return [
      'storeId' => $this->storeId,
      'credential' => $this->credential?->toArray(),
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      storeId:    $data['storeId'] ?? '',
      credential: isset($data['credential']) ? Credential::fromArray($data['credential']) : null,
    );
  }
}