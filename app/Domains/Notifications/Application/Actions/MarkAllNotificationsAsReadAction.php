<?php

declare(strict_types=1);

namespace App\Domains\Notifications\Application\Actions;

use App\Domains\Auth\Domain\Models\User;

final readonly class MarkAllNotificationsAsReadAction
{
    public function execute(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }
}
