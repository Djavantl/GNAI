<?php

declare(strict_types=1);

namespace App\Domains\Backup\Infrastructure\Reporting;

use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class BackupsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'backup.backups';
    }

    public function label(): string
    {
        return 'Backups';
    }

    protected function model(): string
    {
        return Backup::class;
    }

    protected function with(): array
    {
        return ['user'];
    }

    protected function definitions(): array
    {
        return [
            'file_name' => ['label' => 'Arquivo'], 'size' => ['label' => 'Tamanho'],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(BackupStatus::class)],
            'user' => ['label' => 'Criado por', 'path' => 'user.name'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['file_name', 'status', 'user', 'created_at'];
    }
}
