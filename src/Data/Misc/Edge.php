<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Misc;

/**
 * @template T of object
 */
class Edge
{
  /**
   * @param string $cursor
   * @param T $node
   */
  public function __construct(
    public string $cursor,
    public object $node,
  ) {
  }

  /**
   * @return array{cursor: string, node: array}
   */
  public function toArray(): array
  {
    return [
      'cursor' => $this->cursor,
      'node' => $this->node->toArray(),
    ];
  }

  /**
   * @template TNode of object
   * @param array{cursor: string, node: array} $data
   * @param callable(array): TNode $nodeFromArray
   * @return self<TNode>
   */
  public static function fromArray(array $data, callable $nodeFromArray): self
  {
    return new self(
      cursor: $data['cursor'],
      node:   $nodeFromArray($data['node']),
    );
  }
}
