<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()
                    ->route('auth.dashboard')
                    ->with('success', 'login realizado com sucesso.');
            }

            if ($user->role === 'professional' && $user->professional_id) {
                return redirect()
                    ->route('auth.dashboard')
                    ->with('success', 'login realizado com sucesso.');
            }

            // Qualquer outro caso
            Auth::logout();
            return back()->with('error', 'Usuário sem permissão de acesso.');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login');
    }
}

