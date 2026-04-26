{{-- Formulario compartido para crear y editar usuarios --}}
{{-- Requiere: $usuario (null en create), $roles, $action, $method --}}

@php $editing = !is_null($usuario); @endphp

<div class="max-w-3xl mx-auto animate-fade-in">
    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="card">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Datos personales</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" id="nombre" name="nombre" class="input"
                       value="{{ old('nombre', $usuario?->nombre) }}" required>
            </div>

            <div class="form-group">
                <label for="apellido1" class="form-label">Primer apellido *</label>
                <input type="text" id="apellido1" name="apellido1" class="input"
                       value="{{ old('apellido1', $usuario?->apellido1) }}" required>
            </div>

            <div class="form-group">
                <label for="apellido2" class="form-label">Segundo apellido</label>
                <input type="text" id="apellido2" name="apellido2" class="input"
                       value="{{ old('apellido2', $usuario?->apellido2) }}">
            </div>
        </div>

        <div class="form-group mb-8">
            <label for="email" class="form-label">Correo electrónico *</label>
            <input type="email" id="email" name="email" class="input"
                   value="{{ old('email', $usuario?->email) }}" required>
            @unless($editing)
                <small class="text-text-muted mt-1 block">Se enviará una contraseña autogenerada a este correo.</small>
            @endunless
        </div>

        <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Rol *</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
            @php
                $rolActualId = $editing ? $usuario->roles->first()?->id : null;
                $rolSeleccionado = old('role', $rolActualId);
            @endphp
            @foreach($roles as $role)
                <label class="flex items-start gap-3 p-4 rounded-xl bg-bg-medium border border-white/5 cursor-pointer hover:border-primary-400/30 transition">
                    <input type="radio" name="role" value="{{ $role->id }}"
                           class="mt-1"
                           {{ (int) $rolSeleccionado === $role->id ? 'checked' : '' }}
                           required>
                    <div>
                        <div class="font-semibold text-text-primary">{{ $role->display_name }}</div>
                        <div class="text-xs text-text-muted">
                            @if($role->name === 'admin')
                                Acceso completo: gestión de usuarios y todas las funciones del gestor.
                            @else
                                Acceso a clientes, expedientes y su gestión diaria.
                            @endif
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        @if($editing)
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Estado</h3>
            <div class="form-group mb-8">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $usuario->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-text-primary">Cuenta activa (puede iniciar sesión)</span>
                </label>
            </div>
        @endif

        <div class="flex justify-end gap-3 pt-6 border-t border-white/5">
            <a href="{{ route('usuarios') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $editing ? 'Guardar cambios' : 'Crear usuario' }}
            </button>
        </div>
    </form>
</div>
