<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Session;

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

            if ($user->is_admin) {
                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Login realizado com sucesso.');
            }

            if (isset($user->role) && $user->role === 'professional' && $user->professional_id) {
                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Login realizado com sucesso.');
            }

            Auth::logout();
            return back()->with('error', 'Usuário sem permissão de acesso.');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.'
        ]);
    }

    public function index()
    {
        // Buscamos os totais para exibir nos cards do GNAI
        $totalStudents = Student::count();
        $totalSessions = Session::count();

        return view('pages.dashboard', [
            'totalStudents' => $totalStudents,
            'totalSessions' => $totalSessions
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

