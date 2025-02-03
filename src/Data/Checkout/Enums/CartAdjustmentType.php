<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum CartAdjustmentType: string
{
  case CART_TOTAL = 'CART_TOTAL';
  case ITEM = 'ITEM';
  case PROMO_CODE = 'PROMO_CODE';
  case SHIPPING = 'SHIPPING';
}
