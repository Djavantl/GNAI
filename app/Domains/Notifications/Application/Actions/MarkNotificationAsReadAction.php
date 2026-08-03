<?php

declare(strict_types=1);

namespace App\Domains\Notifications\Application\Actions;

use App\Domains\Auth\Domain\Models\User;

final readonly class MarkNotificationAsReadAction
{
    public function execute(User $user, string $notificationId): bool
    {
        $notification = $user
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification === null) {
            return false;
        }

        $notification->markAsRead();

        return true;
    }
}
