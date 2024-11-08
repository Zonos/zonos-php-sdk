<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Connectors\Enums;

enum ZonosConnectorType: string
{
    case Wordpress = 'wordpress';
    case Prestashop = 'prestashop';
    case Magento = 'magento';
    case Default = 'default';
}