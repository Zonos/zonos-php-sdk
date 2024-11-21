<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Utils\GqlBuilder;

abstract class PendingZonosRequest
{
    protected const DEFAULT_ATTRIBUTES = [];

    public function __construct(
        protected ZonosConnector $connector,
        protected GqlBuilder     $query,
    ) {
    }

    protected function normalizeFields(array $fields): array
    {
        if ($fields === ['*'] || empty($fields)) {
            return static::DEFAULT_ATTRIBUTES;
        }

        $fields = $this->dot($fields);

        $fields = array_map(function($field) {
            if (str_contains($field, '.*')) {
                throw new InvalidArgumentException('You cannot use "*" for nested fields.');
            }

            return $field === '*' ? static::DEFAULT_ATTRIBUTES : $field;
        }, $fields);

        return array_values(array_unique($this->flatten($fields)));
    }

    protected function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, $this->dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    protected function flatten(array $array): array
    {
        $result = [];

        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $item);
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }
}