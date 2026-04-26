<?php

namespace App\Http\Controllers;

use App\Mail\UsuarioCreadoMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $mostrarInactivos = $request->boolean('inactivos');

        $query = User::with('roles');

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    "CONCAT(nombre, ' ', COALESCE(apellido1, ''), ' ', COALESCE(apellido2, '')) ILIKE ?",
                    ["%{$search}%"]
                )->orWhereRaw('email ILIKE ?', ["%{$search}%"]);
            });
        }

        if ($mostrarInactivos) {
            $query->onlyTrashed();
        }

        $usuarios = $query->orderBy('nombre')->orderBy('apellido1')->paginate(20);

        return view('usuarios.index', compact('usuarios', 'mostrarInactivos'));
    }

    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|exists:roles,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido1.required' => 'El primer apellido es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no es válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'role.required' => 'Selecciona un rol para el usuario.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        $passwordPlano = Str::password(12, letters: true, numbers: true, symbols: false, spaces: false);

        $user = User::create([
            'nombre' => $validated['nombre'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($passwordPlano),
            'is_active' => true,
        ]);

        $user->roles()->sync([$validated['role']]);

        Mail::to($user->email)->send(new UsuarioCreadoMail($user, $passwordPlano));

        return redirect()
            ->route('usuarios')
            ->with('success', "Usuario creado. Se ha enviado un correo con la contraseña a {$user->email}.");
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('display_name')->get();
        $usuario->load('roles');
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
        ], [
            'email.unique' => 'Ya existe otro usuario con ese correo.',
            'role.required' => 'Selecciona un rol para el usuario.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        // No permitir que un admin se quite el rol admin a sí mismo
        $rolAdmin = Role::where('name', 'admin')->first();
        if ($usuario->id === $request->user()->id && $rolAdmin && (int) $validated['role'] !== $rolAdmin->id) {
            return back()
                ->withInput()
                ->with('error', 'No puedes quitarte el rol de administrador a ti mismo.');
        }

        $usuario->update([
            'nombre' => $validated['nombre'],
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'] ?? null,
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $usuario->roles()->sync([$validated['role']]);

        return redirect()
            ->route('usuarios')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()
            ->route('usuarios')
            ->with('success', "Usuario {$usuario->nombreCompleto} desactivado.");
    }

    public function restore($id)
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()
            ->route('usuarios')
            ->with('success', "Usuario {$usuario->nombreCompleto} reactivado.");
    }
}
