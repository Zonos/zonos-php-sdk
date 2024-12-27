<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout\Enums;

enum OrderStatus: string
{
    case CANCELED = 'CANCELED';
    case COMPLETED = 'COMPLETED';
    case FRAUD_HOLD = 'FRAUD_HOLD';
    case IN_TRANSIT_TO_CONSOLIDATION_CENTER = 'IN_TRANSIT_TO_CONSOLIDATION_CENTER';
    case OPEN = 'OPEN';
    case PARTIALLY_SHIPPED = 'PARTIALLY_SHIPPED';
    case PAYMENT_FAILED = 'PAYMENT_FAILED';
    case PAYMENT_PENDING = 'PAYMENT_PENDING';
} 