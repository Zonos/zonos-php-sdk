<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum PartyType: string
{
  case DESTINATION = 'DESTINATION';
  case ORIGIN = 'ORIGIN';
  case PAYEE = 'PAYEE';
  case PAYOR = 'PAYOR';
}