<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Fakes;

use App\Domains\Backup\Application\Actions\PruneBackupsActionContract;
use RuntimeException;

final class FakePruneBackupsAction implements PruneBackupsActionContract
{
    public bool $shouldFail = false;

    public int $pruned = 0;

    public bool $wasCalled = false;

    public function execute(): int
    {
        $this->wasCalled = true;

        if ($this->shouldFail) {
            throw new RuntimeException('Falha simulada na poda.');
        }

        return $this->pruned;
    }
}
