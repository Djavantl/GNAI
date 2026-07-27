<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Impersonation\LeaveImpersonationAction;
use App\Domains\Auth\Application\Actions\Impersonation\StartImpersonationAction;
use App\Domains\Auth\Domain\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

final class ImpersonationController extends Controller
{
    public function start(User $user, StartImpersonationAction $action, Request $request): RedirectResponse
    {
        $target = $action->execute($request->user(), $user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você entrou como '.$target->name);
    }

    public function leave(LeaveImpersonationAction $action): RedirectResponse
    {
        $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você voltou para admin.');
    }
}
