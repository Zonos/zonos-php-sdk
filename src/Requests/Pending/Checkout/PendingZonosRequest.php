<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use InvalidArgumentException;
use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Utils\GqlBuilder;

/**
 * Abstract class for pending requests in the Zonos SDK
 */
abstract class PendingZonosRequest
{
  protected const DEFAULT_ATTRIBUTES = [];

  /**
   * Constructor for the PendingZonosRequest
   *
   * @param ZonosConnector $connector The checkout connector instance
   * @param GqlBuilder $query The GraphQL query builder instance
   */
  public function __construct(
    protected ZonosConnector $connector,
    protected GqlBuilder     $query,
  ) {
  }

  /**
   * Normalize the fields for the query
   *
   * @param array $fields The fields to normalize
   * @return array
   */
  protected function normalizeFields(array $fields): array
  {
    if ($fields === ['*'] || empty($fields)) {
      return static::DEFAULT_ATTRIBUTES;
    }

    $fields = $this->dot($fields);

    $fields = array_map(
      function ($field) {
        if (str_contains($field, '.*')) {
          throw new InvalidArgumentException('You cannot use "*" for nested fields.');
        }

        return $field === '*' ? static::DEFAULT_ATTRIBUTES : $field;
      }, $fields
    );

    return array_values(array_unique($this->flatten($fields)));
  }

  /**
   * Convert an array to a dot notation
   *
   * @param array $array The array to convert
   * @param string $prepend The prefix to add to the keys
   * @return array
   */
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

  /**
   * Flatten an array
   *
   * @param array $array The array to flatten
   * @return array
   */
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
