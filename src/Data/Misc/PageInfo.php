<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Misc;

class PageInfo
{
  public function __construct(
    public ?string $endCursor,
    public bool    $hasNextPage,
    public bool    $hasPreviousPage,
    public ?string $startCursor,
  ) {
  }

  public function toArray(): array
  {
    return [
      'endCursor' => $this->endCursor,
      'hasNextPage' => $this->hasNextPage,
      'hasPreviousPage' => $this->hasPreviousPage,
      'startCursor' => $this->startCursor,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      endCursor:       $data['endCursor'] ?? null,
      hasNextPage:     $data['hasNextPage'] ?? false,
      hasPreviousPage: $data['hasPreviousPage'] ?? false,
      startCursor:     $data['startCursor'] ?? null,
    );
  }
}
