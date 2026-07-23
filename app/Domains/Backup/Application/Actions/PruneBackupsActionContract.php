<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Actions;

interface PruneBackupsActionContract
{
    public function execute(): int;
}
