<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Impersonation\LeaveImpersonationAction;
use App\Domains\Auth\Application\Actions\Impersonation\StartImpersonationAction;
use App\Domains\Auth\Application\Data\Impersonation\StartImpersonationData;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ImpersonationController
{
    public function start(
        User $user,
        StartImpersonationData $data,
        StartImpersonationAction $action,
        Request $request,
    ): RedirectResponse {
        $target = $action->execute($request->user(), $user, $data->mode);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você entrou como '.$target->name.' ('.mb_strtolower($data->mode->label()).').');
    }

    public function leave(LeaveImpersonationAction $action): RedirectResponse
    {
        $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você voltou para a sua conta de administrador superior.');
    }
}
