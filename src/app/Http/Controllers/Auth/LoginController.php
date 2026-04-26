<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de Autenticación
 * 
 * Gestiona todo el proceso de login, logout y seguridad de sesiones.
 * Incluye protección contra ataques de fuerza bruta mediante throttling
 * y registro de intentos de inicio de sesión.
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Verifica que email y password estén presentes y con formato correcto
        $request->validate([
            'email' => 'required|email',      // Email requerido y formato válido
            'password' => 'required|string',  // Contraseña requerida como texto
        ]);

        // Verifica si la IP ha tenido 10 o más intentos fallidos recientemente
        // Esto previene ataques distribuidos desde la misma IP
        if (LoginAttempt::recentFailedAttemptsByIp($request->ip()) >= 10) {
            abort(429, 'Demasiados intentos desde esta IP. Intentalo de nuevo más tarde.');
        }

        // Cuenta intentos fallidos recientes para este email específico
        $failedAttempts = LoginAttempt::recentFailedAttempts($request->email);
        
        // Si hay 5 o más intentos fallidos, bloquea temporalmente
        if ($failedAttempts >= 5) {
            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos fallidos. Intentalo de nuevo más tarde.',
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Auth::attempt() verifica el email y compara la contraseña hasheada
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Verifica si la cuenta del usuario está activa
            if (!$user->isActive()) {
                // Si está inactiva, cierra la sesión inmediatamente
                Auth::logout();
                
                // Registra el intento fallido (cuenta inactiva)
                LoginAttempt::log(
                    $request->email,
                    $request->ip(),
                    false,  // success = false
                    $request->userAgent()
                );

                // Lanza excepción informando que la cuenta está desactivada
                throw ValidationException::withMessages([
                    'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
                ]);
            }

            // Guarda en la base de datos el intento exitoso de login
            LoginAttempt::log(
                $request->email,
                $request->ip(),
                true,  // success = true
                $request->userAgent()
            );

            // Actualiza la fecha/hora y IP del último login del usuario
            $user->updateLastLogin($request->ip());

            // Regenera el ID de sesión para prevenir ataques de session fixation
            // (evita que un atacante use un ID de sesión predicho)
            $request->session()->regenerate();

            // Redirige a la página que el usuario intentaba acceder originalmente
            // Si no había destino previo, redirige al dashboard
            return redirect()->intended('dashboard');
        }

        
        // Registra el intento fallido para el sistema de throttling
        LoginAttempt::log(
            $request->email,
            $request->ip(),
            false,  // success = false
            $request->userAgent()
        );

        // Lanza excepción de validación con mensaje de error genérico
        throw ValidationException::withMessages([
            'email' => 'Usuario o contraseña incorrectos.',
        ]);
    }

    public function logout(Request $request)
    {
        // Cierra la sesión del usuario autenticado
        Auth::logout();

        // Invalida completamente la sesión actual (borra datos de sesión)
        $request->session()->invalidate();

        // Regenera el token CSRF para la siguiente petición
        // Esto previene que tokens antiguos sean reutilizados
        $request->session()->regenerateToken();

        // Redirige a la página principal (normalmente login)
        return redirect('/');
    }

    public function logoutOtherDevices(Request $request)
    {
        // Requiere que el usuario ingrese su contraseña para confirmar la acción
        $request->validate([
            'password' => 'required|string',
        ]);

        // Verifica que la contraseña proporcionada sea correcta
        // Auth::validate() comprueba credenciales sin iniciar sesión
        if (!Auth::validate([
            'email' => Auth::user()->email,
            'password' => $request->password,
        ])) {
            // Si la contraseña es incorrecta, lanza excepción
            throw ValidationException::withMessages([
                'password' => 'La contraseña es incorrecta.',
            ]);
        }

        // Invalida todas las sesiones del usuario excepto la sesión actual
        // Laravel identifica las sesiones por el password_hash del usuario
        Auth::logoutOtherDevices($request->password);

        // Redirige de vuelta con mensaje de confirmación
        // back() retorna a la página anterior
        // with() añade un mensaje flash a la sesión
        return back()->with('status', 'Sesiones cerradas correctamente.');
    }
}