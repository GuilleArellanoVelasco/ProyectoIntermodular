@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')
@section('page-subtitle', 'Gestiona tu informacion personal y preferencias')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">

    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form id="perfilForm" action="{{ route('perfil.update') }}" method="POST" class="card" novalidate>
        @csrf
        @method('PUT')

        {{-- Avatar y nombre --}}
        @php $user = auth()->user(); @endphp
        <div class="flex items-center gap-6 mb-8 pb-6 border-b border-white/5">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg">
                {{ $user->iniciales }}
            </div>
            <div>
                <h2 class="text-xl font-semibold text-text-primary">{{ $user->nombreCompleto }}</h2>
                <p class="text-sm text-text-secondary">{{ $user->email }}</p>
                @if($user->roles->isNotEmpty())
                    <span class="inline-block mt-2 px-3 py-1 bg-primary-400/10 text-primary-400 text-xs font-medium rounded-full">
                        {{ ucfirst($user->roles->first()->name) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Informacion Personal --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Informacion Personal</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="input" value="{{ auth()->user()->nombre }}">
                    <span class="form-error hidden" id="nombreError"></span>
                </div>

                <div class="form-group">
                    <label for="apellido1" class="form-label">Primer Apellido *</label>
                    <input type="text" id="apellido1" name="apellido1" class="input" value="{{ auth()->user()->apellido1 }}">
                    <span class="form-error hidden" id="apellido1Error"></span>
                </div>

                <div class="form-group">
                    <label for="apellido2" class="form-label">Segundo Apellido</label>
                    <input type="text" id="apellido2" name="apellido2" class="input" value="{{ auth()->user()->apellido2 }}">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo Electronico *</label>
                <input type="email" id="email" name="email" class="input" value="{{ auth()->user()->email }}">
                <span class="form-error hidden" id="emailError"></span>
            </div>
        </div>

        {{-- Cambiar Contraseña --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Cambiar Contraseña</h3>
            <p class="text-sm text-text-secondary mb-5">Deja estos campos vacios si no deseas cambiar tu contraseña.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="form-group">
                    <label for="current_password" class="form-label">Contraseña Actual</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="current_password" name="current_password" class="input pr-12" placeholder="••••••••">
                        <button type="button" class="password-toggle" onclick="togglePasswordField('current_password')">
                            <svg class="icon-eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="icon-eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <span class="form-error hidden" id="current_passwordError"></span>
                </div>
                <div></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password" class="input pr-12" placeholder="••••••••">
                        <button type="button" class="password-toggle" onclick="togglePasswordField('new_password')">
                            <svg class="icon-eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="icon-eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <span class="form-error hidden" id="new_passwordError"></span>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="input pr-12" placeholder="••••••••">
                        <button type="button" class="password-toggle" onclick="togglePasswordField('new_password_confirmation')">
                            <svg class="icon-eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="icon-eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <span class="form-error hidden" id="new_password_confirmationError"></span>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end pt-6 border-t border-white/5">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('perfilForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        isValid = validateRequired('nombre', 'El nombre es obligatorio') && isValid;
        isValid = validateRequired('apellido1', 'El primer apellido es obligatorio') && isValid;
        isValid = validateRequired('email', 'El correo electronico es obligatorio') && isValid;

        const email = document.getElementById('email');
        if (email.value && !isValidEmail(email.value)) {
            showError(email, 'El formato del correo electronico no es valido');
            isValid = false;
        }

        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const currentPassword = document.getElementById('current_password');

        if (newPassword.value || confirmPassword.value) {
            if (!currentPassword.value) {
                showError(currentPassword, 'Debes introducir tu contraseña actual');
                isValid = false;
            }

            if (newPassword.value.length < 8) {
                showError(newPassword, 'La contraseña debe tener al menos 8 caracteres');
                isValid = false;
            }

            if (newPassword.value !== confirmPassword.value) {
                showError(confirmPassword, 'Las contraseñas no coinciden');
                isValid = false;
            }
        }

        if (isValid) {
            form.submit();
        }
    });

    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearError(this);
        });
    });
});

function togglePasswordField(fieldId) {
    const input = document.getElementById(fieldId);
    const wrapper = input.closest('.password-input-wrapper');
    const iconOpen = wrapper.querySelector('.icon-eye-open');
    const iconClosed = wrapper.querySelector('.icon-eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        iconOpen.classList.add('hidden');
        iconClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        iconOpen.classList.remove('hidden');
        iconClosed.classList.add('hidden');
    }
}

function validateRequired(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return true;

    const value = field.value.trim();
    if (value === '') {
        showError(field, message);
        return false;
    }
    return true;
}

function showError(input, message) {
    input.classList.add('input-error');
    const errorSpan = document.getElementById(input.id + 'Error');
    if (errorSpan) {
        errorSpan.textContent = message;
        errorSpan.classList.remove('hidden');
    }
}

function clearError(input) {
    input.classList.remove('input-error');
    const errorSpan = document.getElementById(input.id + 'Error');
    if (errorSpan) {
        errorSpan.classList.add('hidden');
        errorSpan.textContent = '';
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
</script>
@endpush
@endsection
