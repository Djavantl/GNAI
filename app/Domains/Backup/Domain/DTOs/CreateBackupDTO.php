<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\DTOs;

use App\Domains\Backup\Domain\Enums\BackupStatus;

final readonly class CreateBackupDTO
{
    public function __construct(
        public string $fileName,
        public string $filePath,
        public string $size,
        public BackupStatus $status,
        public ?int $userId = null,
    ) {}
}
