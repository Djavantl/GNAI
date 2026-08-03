<?php

declare(strict_types=1);

namespace App\Domains\Notifications\Application\Queries;

use App\Domains\Auth\Domain\Models\User;

final readonly class CountUnreadNotificationsQuery
{
    public function execute(User $user): int
    {
        return $user
            ->unreadNotifications()
            ->count();
    }
}
