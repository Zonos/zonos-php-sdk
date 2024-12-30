<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

class CredentialCreateInput
{
  public function __construct(
    public string $name,
    public string $organization,
    public string $mode,
    public string $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'name' => $this->name,
      'organization' => $this->organization,
      'mode' => $this->mode,
      'type' => $this->type,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      name:         $data['name'],
      organization: $data['organization'],
      mode:         $data['mode'],
      type:         $data['type'],
    );
  }
}