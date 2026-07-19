<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

enum WaitlistStatus: string
{
    case WAITING = 'waiting';
    case NOTIFIED = 'notified';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
}
