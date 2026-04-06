<?php

namespace App\Http\Controllers;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function impersonate(User $user)
    {
        if ($user->is_admin) {
        abort(403, 'Não é possível impersonar outro admin.');
    }

    if ($user->id === auth()->id()) {
        abort(403, 'Não é possível impersonar você mesmo.');
    }

        session(['impersonator_id' => auth()->id()]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Você entrou como ' . $user->name);
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não esta em uma impersonação ');
        }

        $adminId = session('impersonator_id');

        session()->forget('impersonator_id');

        Auth::loginUsingId($adminId);

        return redirect()->route('dashboard')
            ->with('success', 'Você voltou para admin ');
    }
}
