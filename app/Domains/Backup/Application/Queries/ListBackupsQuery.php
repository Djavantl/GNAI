<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Queries;

use App\Domains\Backup\Application\Data\ListBackupsData;
use App\Domains\Backup\Domain\Models\Backup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListBackupsQuery
{
    /**
     * @return LengthAwarePaginator<int, Backup>
     */
    public function execute(ListBackupsData $filters): LengthAwarePaginator
    {
        $name = trim((string) $filters->name);

        return Backup::query()
            ->with('user')
            ->when($name !== '', fn (Builder $query) => $query->where('file_name', 'like', "%{$name}%"))
            ->when($filters->status !== null, fn (Builder $query) => $query->where('status', $filters->status->value))
            ->when($filters->userId !== null, fn (Builder $query) => $query->where('user_id', $filters->userId))
            ->latest()
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
