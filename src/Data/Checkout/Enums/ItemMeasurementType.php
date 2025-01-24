<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum ItemMeasurementType: string
{
  case ALCOHOL_BY_VOLUME = 'ALCOHOL_BY_VOLUME';
  case HEIGHT = 'HEIGHT';
  case LENGTH = 'LENGTH';
  case VOLUME = 'VOLUME';
  case WEIGHT = 'WEIGHT';
  case WIDTH = 'WIDTH';
}
