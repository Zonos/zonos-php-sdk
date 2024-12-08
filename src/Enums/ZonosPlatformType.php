<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Enums;

/**
 * Enum for the platform type
 */
enum ZonosPlatformType: string
{
  case Wordpress = 'wordpress';
  case Prestashop = 'prestashop';
  case Magento = 'magento';
  case Default = 'default';
} 