<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()
            ->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('pages.notifications.index', compact('notifications'));
    }

    // public function markAsRead(string $id)
    // {
    //     $notification = auth()
    //         ->user()
    //         ->notifications()
    //         ->where('id', $id)
    //         ->firstOrFail();

    //     $notification->markAsRead();

    //     return back();
    // }

    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('success', 'Todas notificações foram lidas.');
    }
    
    // retorna contador de notificações não lidas do usuário autenticado
    public function count()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    // lista as notificações (pode paginar)
    public function list()
    {
        $notifications = auth()->user()->notifications()->take(10)->get(); // últimas 10
        return response()->json($notifications);
    }

    // marca uma notificação específica como lida
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json(['error' => 'Notificação não encontrada.'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}