<?php

declare(strict_types=1);

namespace App\Domains\Notifications\Application\Queries;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

final readonly class ListRecentNotificationsQuery
{
    /**
     * @return Collection<int, DatabaseNotification>
     */
    public function execute(User $user): Collection
    {
        return $user
            ->notifications()
            ->take(10)
            ->get();
    }
}
