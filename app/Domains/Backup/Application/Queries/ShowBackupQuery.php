<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Queries;

use App\Domains\Backup\Domain\Models\Backup;

final readonly class ShowBackupQuery
{
    public function execute(Backup $backup): Backup
    {
        return $backup->load('user');
    }
}
