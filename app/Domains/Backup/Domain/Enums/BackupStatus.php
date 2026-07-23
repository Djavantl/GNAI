<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\Enums;

enum BackupStatus: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS => 'Sucesso',
            self::FAILED => 'Falha',
            self::ARCHIVED => 'Arquivado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::ARCHIVED => 'info',
        };
    }

    public function allowsRestore(): bool
    {
        return $this === self::SUCCESS;
    }
}
