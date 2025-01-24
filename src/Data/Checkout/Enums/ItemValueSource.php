<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum ItemValueSource: string
{
  case API_REQUEST = 'API_REQUEST';
  case CATALOG = 'CATALOG';
  case CLASSIFY = 'CLASSIFY';
  case CLASSIFY_ON_THE_FLY = 'CLASSIFY_ON_THE_FLY';
  case FALLBACK = 'FALLBACK';
  case HYBRID = 'HYBRID';
  case ORGANIZATION_SETTING = 'ORGANIZATION_SETTING';
  case TARIFF_COMPLETED = 'TARIFF_COMPLETED';
  case USER_PROVIDED = 'USER_PROVIDED';
}
