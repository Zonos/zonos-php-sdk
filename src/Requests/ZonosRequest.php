<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * ZonosRequest class for handling Zonos API requests
 */
class ZonosRequest extends Request implements HasBody
{
  use HasJsonBody;

  protected Method $method = Method::POST;

  /**
   * Constructor for the ZonosRequest
   *
   * @param string|null $response The response type
   * @param string $gql The GraphQL query
   */
  public function __construct(
    protected ?string $response,
    protected string  $gql,
  ) {
  }

  /**
   * Resolve the endpoint for the request
   *
   * @return string
   */
  public function resolveEndpoint(): string
  {
    return '/graphql';
  }

  /**
   * Default body for the request
   *
   * @return array
   */
  protected function defaultBody(): array
  {
    return ['query' => $this->gql];
  }
}
