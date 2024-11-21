<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ZonosRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected ?string $response,
        protected string $gql,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/graphql';
    }

    protected function defaultBody(): array
    {
        return ['query' => $this->gql];
    }
}