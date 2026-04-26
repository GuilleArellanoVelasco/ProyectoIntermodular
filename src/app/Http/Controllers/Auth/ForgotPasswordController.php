<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }


    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Se ignora intencionalmente el resultado de sendResetLink():
        // mostrar el mismo mensaje tanto si el email existe como si no
        // evita que un atacante enumere cuentas válidas del sistema.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña en unos minutos.');
    }
}