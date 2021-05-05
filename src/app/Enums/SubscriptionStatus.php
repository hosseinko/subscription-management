<?php


namespace App\Enums;

use MadWeb\Enum\Enum;

class SubscriptionStatus extends Enum
{
    const ACTIVE   = 'active';
    const EXPIRED  = 'expired';
    const CANCELED = 'canceled';
}
