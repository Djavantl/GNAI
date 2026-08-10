<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Contracts;

interface PruneBackupsActionContract
{
    public function execute(): int;
}
