<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Utils;

/**
 * Builder class for constructing GraphQL queries
 */
class GqlBuilder
{
  /**
   * Create a new GqlBuilder instance
   *
   * @param string $type Query type (query/mutation)
   * @param string $name Operation name
   * @param array|null $args Query arguments
   * @return static
   */
  public static function make(string $type, string $name, ?array $args = null): static
  {
    $instance = new static($type, $name);

    if ($args) {
      $instance->withArguments($args);
    }

    return $instance;
  }

  /**
   * Constructor for the GqlBuilder
   *
   * @param string $type Query type (query/mutation)
   * @param string $name Operation name
   * @param array $fields Query fields
   * @param array $arguments Query arguments
   * @param string|null $alias Query alias
   */
  public function __construct(
    protected string  $type,
    protected string  $name,
    protected array   $fields = [],
    protected array   $arguments = [],
    protected ?string $alias = null,
  ) {
  }

  /**
   * Add fields to the query
   *
   * @param array $fields Query fields
   * @param bool $overwrite Whether to overwrite existing fields
   * @return static
   */
  public function withFields(array $fields, bool $overwrite = false): static
  {
    $this->fields = $overwrite
      ? $fields
      : array_merge($this->fields, $fields);

    return $this;
  }

  /**
   * Add arguments to the query
   *
   * @param array $arguments Query arguments
   * @param bool $overwrite Whether to overwrite existing arguments
   * @return static
   */
  public function withArguments(array $arguments, bool $overwrite = false): static
  {
    $arguments = array_filter($arguments, fn($arg) => null !== $arg);

    $this->arguments = $overwrite
      ? $arguments
      : array_replace_recursive($this->arguments, $arguments);

    return $this;
  }


  /**
   * Convert the query to a string
   *
   * @return string
   */
  public function __toString(): string
  {
    $alias = $this->alias
      ? " {$this->alias}"
      : '';
    $args = $this->formatArguments($this->arguments);
    $fields = $this->formatFields($this->fields);

    $args = str_replace('"LIVE"', 'LIVE', $args); // TODO: Revisar esto con Hiram
    $args = str_replace('"TEST"', 'TEST', $args); // TODO: Revisar esto con Julio
    $args = str_replace('"PUBLIC_TOKEN"', 'PUBLIC_TOKEN', $args); // TODO: Revisar esto con Hiram

    return <<<gql
		{$this->type}{$alias} {
			{$this->name}{$args} {
				{$fields}
			}
		}
		gql;
  }

  /**
   * Format the arguments for the query
   *
   * @param array $arguments Query arguments
   * @param int $depth Depth of the arguments
   * @return string
   */
  protected function formatArguments(array $arguments, int $depth = 0): string
  {
    if (!count($arguments)) {
      return '';
    }

    $indent = "\t\t" . (str_repeat("\t", $depth));

    $result = array_map(
      function ($value, $key) use ($depth, $indent) {
        $line = "{$key}: ";

        if (is_object($value)) {
          $value = array_filter((array)$value);
        }

        if (is_array($value)) {
          $line .= "{\n{$indent}\t" . $this->formatArguments($value, $depth + 1) . "\n{$indent}}";
        } else {
          $line .= json_encode($value);
        }

        return $line;
      }, $arguments, array_keys($arguments)
    );

    $resultString = implode(",\n{$indent}", $result);

    if ($depth) {
      return $resultString;
    }

    return str_contains($resultString, "\n")
      ? "(\n{$indent}{$resultString}\n\t)"
      : "({$resultString})";
  }

  /**
   * Format the fields for the query
   *
   * @param array $keys Query fields
   * @param int $depth Depth of the fields
   * @return string
   */
  protected function formatFields(array $keys, int $depth = 0): string
  {
    $indent = "\t\t" . (str_repeat("\t", $depth));

    if ($depth === 0) {
      $keys = array_flip($keys);

      $keys = $this->undot($keys);
    }


    $result = array_map(
      function ($value, $key) use ($depth, $indent) {
        $line = $key;

        if (is_array($value)) {
          $line .= " {\n{$indent}\t" . $this->formatFields($value, $depth + 1) . "\n{$indent}}";
        }

        return $line;
      }, $keys, array_keys($keys)
    );

    return implode("\n{$indent}", $result);
  }

  /**
   * Undot the array
   *
   * @param array $array The array to undot
   * @return array
   */
  protected function undot(array $array): array
  {
    $result = [];
    foreach ($array as $key => $value) {
      $keys = explode('.', $key);
      $temp = &$result;

      while ($segment = array_shift($keys)) {
        if (!isset($temp[$segment])) {
          $temp[$segment] = [];
        }
        $temp = &$temp[$segment];
      }

      $temp = $value;
    }
    return $result;
  }
}