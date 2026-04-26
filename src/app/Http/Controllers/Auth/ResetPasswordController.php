<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRules;

/**
 * Controlador de Restablecimiento de Contraseña
 * 
 * Gestiona el proceso final de cambio de contraseña después de que el usuario
 * ha recibido y clickeado el enlace de recuperación en su email.
 * 
 * Flujo completo:
 * 1. Usuario recibe email con enlace: /reset-password/{token}?email={email}
 * 2. Hace clic y llega a showResetForm() que muestra el formulario
 * 3. Usuario ingresa nueva contraseña (2 veces para confirmar)
 * 4. reset() valida el token, actualiza la contraseña y cierra otras sesiones
 * 5. Redirige al login con mensaje de éxito
 */
class ResetPasswordController extends Controller
{

    public function showResetForm(Request $request, string $token)
    {
        // Renderiza la vista del formulario de reset
        // Pasa el token y email a la vista para incluirlos en el formulario
        return view('auth.reset-password', [
            'token' => $token,              // Token de la URL
            'email' => $request->email,     // Email de los parámetros GET (?email=...)
        ]);
    }


    public function reset(Request $request)
    {
        // ====================================================================
        // VALIDACIÓN DE DATOS
        // ====================================================================
        $request->validate([
            // Token: requerido (viene del formulario oculto)
            'token' => 'required',
            
            // Email: requerido y formato válido
            'email' => 'required|email',
            
            // Password: requerido, debe coincidir con password_confirmation
            // y cumplir con las reglas de complejidad definidas
            'password' => [
                'required',
                'confirmed',  // Verifica que password === password_confirmation
                PasswordRules::defaults()  // Reglas: mín 8 caracteres, letras, números, símbolos
            ],
        ]);

        // ====================================================================
        // PROCESO DE RESTABLECIMIENTO
        // ====================================================================
        // Password::reset() ejecuta todo el proceso de forma segura:
        // 
        // 1. Busca el token en la tabla 'password_reset_tokens'
        // 2. Verifica que el token hasheado coincida
        // 3. Verifica que el token no haya expirado (< 60 minutos)
        // 4. Busca el usuario por email
        // 5. Si todo es válido, ejecuta el callback
        // 6. Elimina el token de la BD (no se puede reutilizar)
        // 
        // Retorna una constante indicando el resultado
        $status = Password::reset(
            // Solo extrae los campos necesarios del request
            $request->only('email', 'password', 'password_confirmation', 'token'),
            
            // ================================================================
            // CALLBACK: Se ejecuta solo si el token es válido
            // ================================================================
            // Este código se ejecuta ÚNICAMENTE si:
            // - El token es válido
            // - El token no ha expirado
            // - El email existe en la BD
            function ($user, $password) {
                // ============================================================
                // ACTUALIZACIÓN DE CONTRASEÑA
                // ============================================================
                // forceFill() permite asignar valores incluso si están protegidos
                // Actualiza la contraseña del usuario
                $user->forceFill([
                    // El password se hashea automáticamente gracias al cast
                    // definido en el modelo User:
                    // protected $casts = ['password' => 'hashed'];
                    'password' => $password,
                ])->save();

                // ============================================================
                // INVALIDAR SESIONES ANTERIORES (SEGURIDAD)
                // ============================================================
                // El remember_token se usa para la funcionalidad "Recordarme"
                // 
                // Al cambiar este token:
                // - Todas las sesiones "recordarme" en otros dispositivos
                //   quedan invalidadas
                // - El usuario debe volver a iniciar sesión en todos sus
                //   dispositivos
                // - Esto previene que un atacante que haya robado la sesión
                //   pueda seguir accediendo después del cambio de contraseña
                // 
                // Genera un nuevo token aleatorio de 60 caracteres
                $user->setRememberToken(Str::random(60));
                $user->save();

                // ============================================================
                // DISPARAR EVENTO DE AUDITORÍA
                // ============================================================
                // Lanza el evento PasswordReset que puede ser escuchado por:
                // - Listeners que envían emails de confirmación
                // - Sistemas de logging/auditoría
                // - Notificaciones de seguridad
                // - Webhooks a servicios externos
                // 
                // Ejemplo de uso:
                // "Te informamos que tu contraseña fue cambiada el 05/11/2025"
                event(new PasswordReset($user));
            }
        );

        // ====================================================================
        // VERIFICACIÓN DEL RESULTADO
        // ====================================================================
        // Password::PASSWORD_RESET indica que todo fue exitoso
        if ($status === Password::PASSWORD_RESET) {
            // Redirige a la página de login con mensaje de éxito
            // El usuario debe iniciar sesión con su nueva contraseña
            return redirect()->route('login')->with('status', __($status));
        }

        // ====================================================================
        // MANEJO DE ERRORES
        // ====================================================================
        // Si $status no es PASSWORD_RESET, hubo un error:
        // 
        // Posibles valores de $status:
        // - Password::INVALID_TOKEN: Token inválido o ya usado
        // - Password::INVALID_USER: Email no encontrado
        // - Password::RESET_THROTTLED: Demasiados intentos
        // 
        // Lanza una excepción de validación con el mensaje traducido
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}