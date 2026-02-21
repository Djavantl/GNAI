<?php

namespace App\Enums\InclusiveRadar;

enum WaitlistStatus: string
{
    case WAITING   = 'waiting';
    case NOTIFIED  = 'notified';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::WAITING   => 'Em Espera',
            self::NOTIFIED  => 'Notificado',
            self::FULFILLED => 'Atendido',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::WAITING   => 'warning',
            self::NOTIFIED  => 'info',
            self::FULFILLED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
