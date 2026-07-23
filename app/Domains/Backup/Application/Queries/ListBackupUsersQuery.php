<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListBackupUsersQuery
{
    /**
     * @return Collection<int, User>
     */
    public function execute(): Collection
    {
        return User::query()
            ->whereHas('backups')
            ->orderBy('name')
            ->get();
    }
}
