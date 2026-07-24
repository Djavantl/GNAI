<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Controllers;

use App\Domains\Auth\Application\Actions\Sessions\AuthenticateUserAction;
use App\Domains\Auth\Application\Actions\Sessions\LogoutUserAction;
use App\Domains\Auth\Application\Data\Sessions\LoginData;
use App\Domains\Auth\Domain\Exceptions\InactiveProfessionalAccount;
use App\Domains\Auth\Domain\Exceptions\InvalidCredentials;
use App\Domains\Auth\Domain\Exceptions\UserWithoutAccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginData $data, AuthenticateUserAction $action): RedirectResponse
    {
        try {
            $action->execute($data);
        } catch (InvalidCredentials|InactiveProfessionalAccount $exception) {
            return back()
                ->withInput(['email' => $data->email])
                ->withErrors(['email' => $exception->getMessage()]);
        } catch (UserWithoutAccess $exception) {
            return back()
                ->withInput(['email' => $data->email])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login realizado com sucesso.');
    }

    public function logout(Request $request, LogoutUserAction $action): RedirectResponse
    {
        $action->execute();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
