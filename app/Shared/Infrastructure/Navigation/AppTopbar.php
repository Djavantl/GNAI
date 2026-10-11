<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Navigation;

use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\Notifications\UI\Presenters\NotificationDestination;
use Illuminate\Http\Request;

final class AppTopbar
{
    public function forRequest(Request $request): ?array
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        return [
            'institutionName' => Institution::query()->first()?->name ?? 'Gnai',
            'unreadCount' => $user->unreadNotifications()->count(),
            'notifications' => $user->unreadNotifications()->latest()->take(5)->get()->map(fn ($notification) => [
                'iconClass' => 'icon-bell text-custom',
                'title' => $notification->data['title'] ?? 'Nova atualização',
                'description' => $notification->data['message'] ?? '',
                'href' => NotificationDestination::toLocalUrl($notification->data['url'] ?? null) ?? route('notifications.index', absolute: false),
            ])->all(),
        ];
    }
}
