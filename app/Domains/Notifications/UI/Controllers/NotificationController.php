<?php

declare(strict_types=1);

namespace App\Domains\Notifications\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Notifications\Application\Actions\MarkAllNotificationsAsReadAction;
use App\Domains\Notifications\Application\Actions\MarkNotificationAsReadAction;
use App\Domains\Notifications\Application\Queries\CountUnreadNotificationsQuery;
use App\Domains\Notifications\Application\Queries\ListNotificationsQuery;
use App\Domains\Notifications\Application\Queries\ListRecentNotificationsQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NotificationController extends Controller
{
    public function index(
        ListNotificationsQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): View {
        $notifications = $query->execute($authenticatedUser->fromRequest($request));

        return view('pages.notifications.index', compact('notifications'));
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
