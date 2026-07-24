<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Passwords\SendPasswordResetLinkAction;
use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

final class ForgotPasswordController extends Controller
{
    public function showForm(): View
    {
        return view('auth.forgot-password');
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
