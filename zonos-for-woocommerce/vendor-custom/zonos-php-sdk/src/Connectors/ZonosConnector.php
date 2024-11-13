<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Saloon\Http\Connector;
use Zonos\ZonosSdk\Connectors\Enums\ZonosConnectorType;


class ZonosConnector extends Connector
{
    use ZonosQueries;
    use ZonosMutations;

    private ZonosConnectorType $type;
    public function __construct(
        protected string $credential_token,
        protected string $base_url,
        ZonosConnectorType $zonosConnectorType = ZonosConnectorType::Default
    ) {
        $this->type = $zonosConnectorType;
    }

    public function getType(): ZonosConnectorType
    {
        return $this->type;
    }

    public function resolveBaseUrl(): string
    {
        return $this->base_url;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'credentialToken' => $this->credential_token
        ];
    }
}