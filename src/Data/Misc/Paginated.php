<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Misc;

/**
 * @template T of object
 */
class Paginated
{
  /**
   * @param Edge<T>[] $edges
   * @param int $totalCount
   */
  public function __construct(
    public array    $edges,
    public int      $totalCount,
    public PageInfo $pageInfo,
  ) {
  }

  /**
   * @return array{edges: array<array{cursor: string, node: array}>, totalCount: int, pageInfo: array{endCursor: string|null, hasNextPage: bool, hasPreviousPage: bool, startCursor: string|null}}
   */
  public function toArray(): array
  {
    return [
      'edges' => array_map(fn(Edge $edge) => $edge->toArray(), $this->edges),
      'totalCount' => $this->totalCount,
      'pageInfo' => $this->pageInfo->toArray(),
    ];
  }

  /**
   * @template U of object
   * @param array{edges: array<array{cursor: string, node: array}>, totalCount: int, pageInfo: array{endCursor: string|null, hasNextPage: bool, hasPreviousPage: bool, startCursor: string|null}} $data
   * @param callable(array): U $itemFromArray
   * @return self<U>
   */
  public static function fromArray(array $data, callable $itemFromArray): self
  {
    return new self(
      edges:      array_map(fn(array $edge) => Edge::fromArray($edge, $itemFromArray), $data['edges']),
      totalCount: $data['totalCount'],
      pageInfo:   PageInfo::fromArray($data['pageInfo']),
    );
  }
}

