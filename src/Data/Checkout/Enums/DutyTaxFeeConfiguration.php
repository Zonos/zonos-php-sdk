<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum DutyTaxFeeConfiguration: string
{
  case EXCLUDE_ALL = 'EXCLUDE_ALL';
  case EXCLUDE_DUTY = 'EXCLUDE_DUTY';
  case EXCLUDE_FEE = 'EXCLUDE_FEE';
  case EXCLUDE_TAX = 'EXCLUDE_TAX';
  case INCLUDE_ALL = 'INCLUDE_ALL';
  case INCLUDE_TAX = 'INCLUDE_TAX';
}
