<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors;

use Zonos\ZonosSdk\Connectors\Enums\ZonosConnectorType;

class ZonosConnectorFactory
{
    public static function createConnector(ZonosConnectorType $type, string $credential_token, string $base_url): ZonosConnector
    {
        return match($type) {
            ZonosConnectorType::Wordpress => new ZonosConnector($credential_token, $base_url, ZonosConnectorType::Wordpress),
            default => new ZonosConnector($credential_token, $base_url),
        };
    }
}