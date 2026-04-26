<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Validation\ValidationException;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => ['nullable', 'confirmed', PasswordRules::defaults()],
        ], [
            'email.unique' => 'Ya existe otro usuario con ese correo.',
            'current_password.required_with' => 'Introduce tu contraseña actual para cambiarla.',
            'new_password.confirmed' => 'La nueva contraseña no coincide con la confirmación.',
        ]);

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'La contraseña actual no es correcta.',
                ]);
            }
            $user->password = $validated['new_password'];
        }

        $user->nombre = $validated['nombre'];
        $user->apellido1 = $validated['apellido1'];
        $user->apellido2 = $validated['apellido2'] ?? null;
        $user->email = $validated['email'];
        $user->save();

        return redirect()
            ->route('perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
