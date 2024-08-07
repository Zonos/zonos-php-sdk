<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Utils;


class GqlBuilder
{
    public static function make(string $type, string $name, ?array $args = null): static
    {
        $instance = new static($type, $name);

        if ($args) {
            $instance->withArguments($args);
        }

        return $instance;
    }

    public function __construct(
        protected string $type,
        protected string $name,
        protected array $fields = [],
        protected array $arguments = [],
        protected ?string $alias = null,
    ) {
    }

    public function withFields(array $fields, bool $overwrite = false): static
    {
        $this->fields = $overwrite
            ? $fields
            : array_merge($this->fields, $fields);

        return $this;
    }

    public function withArguments(array $arguments, bool $overwrite = false): static
    {
        $arguments = array_filter($arguments, fn($arg) => null !== $arg);

        $this->arguments = $overwrite
            ? $arguments
            : array_replace_recursive($this->arguments, $arguments);

        return $this;
    }


    public function __toString(): string
    {
        $alias = $this->alias
            ? " {$this->alias}"
            : '';
        $args = $this->formatArguments($this->arguments);
        $fields = $this->formatFields($this->fields);

        return <<<gql
		{$this->type}{$alias} {
			{$this->name}{$args} {
				{$fields}
			}
		}
		gql;
    }

    protected function formatArguments(array $arguments, int $depth = 0): string
    {
        if (! count($arguments)) {
            return '';
        }

        $indent = "\t\t".(str_repeat("\t", $depth));

        $result = array_map(function ($value, $key) use ($depth, $indent) {
            $line = "{$key}: ";

            if (is_object($value)) {
                $value = array_filter((array) $value);
            }

            if (is_array($value)) {
                $line .= "{\n{$indent}\t".$this->formatArguments($value, $depth + 1)."\n{$indent}}";
            } else {
                $line .= json_encode($value);
            }

            return $line;
        }, $arguments, array_keys($arguments));

        $resultString = implode(",\n{$indent}", $result);

        if ($depth) {
            return $resultString;
        }

        return str_contains($resultString, "\n")
            ? "(\n{$indent}{$resultString}\n\t)"
            : "({$resultString})";
    }

    protected function formatFields(array $keys, int $depth = 0): string
    {
        $indent = "\t\t".(str_repeat("\t", $depth));

        if ($depth === 0) {
            $keys = array_flip($keys);

            $keys = $this->undot($keys);
        }


        $result = array_map(function($value, $key) use ($depth, $indent) {
            $line = $key;

            if (is_array($value)) {
                $line .= " {\n{$indent}\t" . $this->formatFields($value, $depth + 1) . "\n{$indent}}";
            }

            return $line;
        }, $keys, array_keys($keys));

        return implode("\n{$indent}", $result);
    }

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