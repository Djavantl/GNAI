<?php

declare(strict_types=1);

namespace App\Domains\Notifications\Application\Queries;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListNotificationsQuery
{
    public function execute(User $user): LengthAwarePaginator
    {
        return $user
            ->notifications()
            ->latest()
            ->paginate(10);
    }
}
