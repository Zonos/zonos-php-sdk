<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum Mode: string
{
  case TEST = 'TEST';
  case LIVE = 'LIVE';
}