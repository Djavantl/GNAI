<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Navigation;

use App\Domains\Inclusion\Domain\Models\Institution;
use App\Domains\Notifications\UI\Presenters\NotificationDestination;
use Illuminate\Http\Request;

final class AppTopbar
{
    public function __construct(
        private readonly AppNavigation $navigation,
    ) {}

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
                // Ícone da seção da sidebar à qual a notificação leva, no tamanho do Ubold (fa-2x).
                'iconClass' => ($this->navigation->iconForUrl($notification->data['url'] ?? null) ?? 'ti-bell').' fa-2x text-gnai',
                'title' => $notification->data['title'] ?? 'Nova atualização',
                'description' => $notification->data['message'] ?? '',
                'href' => NotificationDestination::toLocalUrl($notification->data['url'] ?? null) ?? route('notifications.index', absolute: false),
            ])->all(),
        ];
    }
}
