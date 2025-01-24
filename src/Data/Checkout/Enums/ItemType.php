<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum ItemType: string
{
  case BUNDLE = 'BUNDLE';
  case DIGITAL_GOOD = 'DIGITAL_GOOD';
  case PARTIAL_ITEM = 'PARTIAL_ITEM';
  case PHYSICAL_GOOD = 'PHYSICAL_GOOD';
  case SERVICE = 'SERVICE';
  case SUBSCRIPTION = 'SUBSCRIPTION';
}
