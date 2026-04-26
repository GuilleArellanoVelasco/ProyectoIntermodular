<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restablecer Contraseña - Liberxo</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-bg-darkest via-bg-dark to-[#0a0e1a] relative overflow-hidden">
    {{-- Decorative background --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-[600px] h-[600px] rounded-full bg-primary-400/5 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md p-6 relative z-10">
        <div class="bg-bg-dark/90 backdrop-blur-xl border border-primary-400/20 rounded-2xl shadow-dark-xl overflow-hidden animate-slide-up">
            {{-- Header --}}
            <div class="text-center py-8 px-6 pb-6 border-b border-white/5">
                <h1 class="font-display text-4xl font-bold text-primary-400 mb-2">
                    <span class="text-primary-400">Liber</span><span class="text-text-primary">xo</span>
                </h1>
                <p class="text-sm text-text-secondary">Sistema de Gestión de Clientes y Expedientes</p>
            </div>

            {{-- Content --}}
            <div class="p-8">
                <h2 class="text-2xl font-bold text-text-primary mb-2 text-center">Restablecer contraseña</h2>
                <p class="text-sm text-text-secondary text-center mb-8">Introduce tu nueva contraseña para recuperar el acceso a tu cuenta</p>

                @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" id="resetPasswordForm" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <input type="email" id="email" name="email" class="input"
                            placeholder="nombre@empresa.com" value="{{ old('email', $email) }}" required readonly>
                        <span class="form-error hidden" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <div class="password-input-wrapper">
                            <input type="password" name="password" id="passwordInput" class="input pr-12"
                                placeholder="Nueva contraseña" required autofocus>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordInput', 'iconOpen', 'iconClosed')">
                                <svg id="iconOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="iconClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <span class="form-error hidden" id="passwordError"></span>
                    </div>

                    <div class="form-group">
                        <div class="password-input-wrapper">
                            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="input pr-12"
                                placeholder="Confirma la nueva contraseña" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordConfirmInput', 'iconOpenConfirm', 'iconClosedConfirm')">
                                <svg id="iconOpenConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="iconClosedConfirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <span class="form-error hidden" id="passwordConfirmError"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Restablecer contraseña
                    </button>

                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-primary-400 py-3 hover:text-primary-300 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Volver a inicio de sesión
                    </a>
                </form>
            </div>

            {{-- Footer --}}
            <div class="py-4 px-8 border-t border-white/5 text-center">
                <p class="text-xs text-text-muted">&copy; 2025 Liberxo. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, openId, closedId) {
            const input = document.getElementById(inputId);
            const iconOpen = document.getElementById(openId);
            const iconClosed = document.getElementById(closedId);

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

        const form = document.getElementById('resetPasswordForm');
        const email = document.getElementById('email');
        const password = document.getElementById('passwordInput');
        const passwordConfirm = document.getElementById('passwordConfirmInput');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const passwordConfirmError = document.getElementById('passwordConfirmError');

        password.addEventListener('input', function() {
            clearError(this, passwordError);
        });

        passwordConfirm.addEventListener('input', function() {
            clearError(this, passwordConfirmError);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            if (email.value.trim() === '' || !isValidEmail(email.value)) {
                showError(email, emailError, 'El correo no es válido');
                isValid = false;
            }

            if (password.value.trim() === '') {
                showError(password, passwordError, 'La contraseña es obligatoria');
                isValid = false;
            } else if (password.value.length < 8) {
                showError(password, passwordError, 'La contraseña debe tener al menos 8 caracteres');
                isValid = false;
            }

            if (passwordConfirm.value.trim() === '') {
                showError(passwordConfirm, passwordConfirmError, 'Debes confirmar la contraseña');
                isValid = false;
            } else if (password.value !== passwordConfirm.value) {
                showError(passwordConfirm, passwordConfirmError, 'Las contraseñas no coinciden');
                isValid = false;
            }

            if (isValid) {
                this.submit();
            }
        });

        function showError(input, errorElement, message) {
            input.classList.add('input-error');
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }

        function clearError(input, errorElement) {
            input.classList.remove('input-error');
            errorElement.classList.add('hidden');
            errorElement.textContent = '';
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    </script>
</body>

</html>
