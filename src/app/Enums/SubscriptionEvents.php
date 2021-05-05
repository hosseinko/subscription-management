<?php


namespace App\Enums;

use MadWeb\Enum\Enum;

class SubscriptionEvents extends Enum
{
    const STARTED  = 'started';
    const RENEWED  = 'renewed';
    const CANCELED = 'canceled';

}
