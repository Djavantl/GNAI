<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Passwords\SendPasswordResetLinkAction;
use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ForgotPasswordController
{
    public function showForm(): InertiaResponse
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendLink(SendPasswordResetLinkData $data, SendPasswordResetLinkAction $action): RedirectResponse
    {
        $result = $action->execute($data);

        if (! $result->successfulLinkSent() && $result->status !== Password::INVALID_USER) {
            return back()
                ->withInput()
                ->withErrors(['email' => __($result->status)]);
        }

        return back()->with(
            'status',
            'Se o e-mail estiver cadastrado, enviaremos instruções.',
        );
    }
}
