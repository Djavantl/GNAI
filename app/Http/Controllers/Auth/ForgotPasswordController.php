<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'status',
            'Se o e-mail estiver cadastrado, enviaremos instruções.'
        );
    }
}
