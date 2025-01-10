<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

class CredentialServiceTokenQueryFilter
{
  public function __construct(
    public string $mode,
    public string $organizationId,
    public int    $storeId,
    public string $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'mode' => $this->mode,
      'organizationId' => $this->organizationId,
      'storeId' => $this->storeId,
      'type' => $this->type,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      mode:           $data['mode'] ?? '',
      organizationId: $data['organizationId'] ?? '',
      storeId:        $data['storeId'] ?? 0,
      type:           $data['type'] ?? '',
    );
  }
}
