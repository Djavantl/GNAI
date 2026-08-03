<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Passwords\ResetPasswordAction;
use App\Domains\Auth\Application\Data\Passwords\ResetPasswordData;
use App\Domains\Auth\Domain\Exceptions\InvalidPasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ResetPasswordController
{
    public function showForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function reset(ResetPasswordData $data, ResetPasswordAction $action): RedirectResponse
    {
        try {
            $action->execute($data);
        } catch (InvalidPasswordReset $exception) {
            return back()
                ->withInput([
                    'token' => $data->token,
                    'email' => $data->email,
                ])
                ->withErrors(['email' => $exception->getMessage()]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Senha alterada!');
    }
}
