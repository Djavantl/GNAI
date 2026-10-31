<?php

declare(strict_types=1);

namespace App\Domains\Notifications\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Notifications\Application\Actions\MarkAllNotificationsAsReadAction;
use App\Domains\Notifications\Application\Actions\MarkNotificationAsReadAction;
use App\Domains\Notifications\Application\Queries\CountUnreadNotificationsQuery;
use App\Domains\Notifications\Application\Queries\ListNotificationsQuery;
use App\Domains\Notifications\Application\Queries\ListRecentNotificationsQuery;
use App\Domains\Notifications\UI\Presenters\NotificationDestination;
use App\Shared\Infrastructure\Navigation\AppNavigation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class NotificationController
{
    public function index(
        ListNotificationsQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
        AppNavigation $navigation,
    ): InertiaResponse {
        $notifications = $query
            ->execute($authenticatedUser->fromRequest($request))
            ->through(fn (DatabaseNotification $notification): array => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notificação',
                'icon_class' => $navigation->iconForUrl($notification->data['url'] ?? null) ?? 'ti-bell',
                'message' => $notification->data['message'] ?? '',
                'url' => NotificationDestination::toLocalUrl($notification->data['url'] ?? null),
                'is_unread' => $notification->read_at === null,
                'created_at' => $notification->created_at?->toIso8601String(),
                'created_at_human' => $notification->created_at?->diffForHumans(),
            ]);

        return Inertia::render('Notifications/Index', compact('notifications'));
    }

    public function markAllAsRead(
        MarkAllNotificationsAsReadAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($authenticatedUser->fromRequest($request));

        return back()->with('success', 'Todas notificações foram lidas.');
    }

    public function count(
        CountUnreadNotificationsQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): JsonResponse {
        return response()->json([
            'count' => $query->execute($authenticatedUser->fromRequest($request)),
        ]);
    }

    public function list(
        ListRecentNotificationsQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): JsonResponse {
        return response()->json($query->execute($authenticatedUser->fromRequest($request)));
    }

    public function markAsRead(
        string $id,
        MarkNotificationAsReadAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        if (! $action->execute($authenticatedUser->fromRequest($request), $id)) {
            return back()->with('error', 'Notificação não encontrada.');
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notificação marcada como lida.');
    }
}
