<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Auth;

use Zonos\ZonosSdk\Data\Auth\Enums\CredentialType;
use Zonos\ZonosSdk\Data\Auth\Enums\Mode;

class CredentialCreateInput
{
  public function __construct(
    public ?Mode           $mode,
    public ?string         $name,
    public ?string         $organization,
    public ?CredentialType $type,
  ) {
  }

  public function toArray(): array
  {
    return [
      'mode' => $this->mode?->value,
      'name' => $this->name,
      'organization' => $this->organization,
      'type' => $this->type?->value,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      mode:         isset($data['mode']) ? Mode::from($data['mode']) : null,
      name:         $data['name'],
      organization: $data['organization'],
      type:         isset($data['type']) ? CredentialType::from($data['type']) : null,
    );
  }
}
