<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum WebhookStatus: string
{
  case DISABLED = 'DISABLED';
  case ENABLED = 'ENABLED';
}
