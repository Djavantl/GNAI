<?php

namespace App\Enums\InclusiveRadar;

enum MaintenanceStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::COMPLETED => 'Concluída',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
        };
    }
}
