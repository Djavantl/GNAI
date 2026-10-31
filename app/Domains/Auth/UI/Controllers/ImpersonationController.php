<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Impersonation\LeaveImpersonationAction;
use App\Domains\Auth\Application\Actions\Impersonation\StartImpersonationAction;
use App\Domains\Auth\Domain\Enums\ImpersonationMode;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class ImpersonationController
{
    public function start(User $user, StartImpersonationAction $action, Request $request): RedirectResponse
    {
        $validated = $request->validate(
            ['mode' => ['required', Rule::enum(ImpersonationMode::class)]],
            [
                'mode.required' => 'Escolha o modo de acesso.',
                'mode.enum' => 'Modo de acesso inválido.',
            ],
        );
        $mode = ImpersonationMode::from($validated['mode']);
        $target = $action->execute($request->user(), $user, $mode);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você entrou como '.$target->name.' ('.mb_strtolower($mode->label()).').');
    }

    public function leave(LeaveImpersonationAction $action): RedirectResponse
    {
        $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Você voltou para a sua conta de administrador superior.');
    }
}
