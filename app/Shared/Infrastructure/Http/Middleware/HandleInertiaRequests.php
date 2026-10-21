<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Middleware;

use App\Shared\Infrastructure\Navigation\AppNavigation;
use App\Shared\Infrastructure\Navigation\AppTopbar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'inertia';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()?->only([
                    'id', 'name', 'email', 'is_admin', 'role',
                    'professional_id', 'teacher_id', 'photo_url',
                ]),
            ],
            'navigation' => fn () => app(AppNavigation::class)->forRequest($request),
            'topbar' => fn () => app(AppTopbar::class)->forRequest($request),
            'impersonating' => fn () => $request->session()->has('impersonator_id'),
            'flash' => fn () => [
                'response_id' => (string) Str::uuid(),
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'status' => $request->session()->get('status'),
            ],
        ];
    }
}
