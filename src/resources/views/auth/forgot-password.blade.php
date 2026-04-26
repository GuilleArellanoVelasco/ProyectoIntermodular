<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar Contraseña - Liberxo</title>

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
                <h2 class="text-2xl font-bold text-text-primary mb-2 text-center">Recuperar contraseña</h2>
                <p class="text-sm text-text-secondary text-center mb-8">Ingresa tu correo y te enviaremos un enlace para recuperar tu contraseña</p>

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5" id="forgotPasswordForm" novalidate>
                    @csrf

                    <div class="form-group">
                        <input type="email" id="email" name="email" class="input"
                            placeholder="nombre@empresa.com" value="{{ old('email') }}" required autofocus>
                        <span class="form-error hidden" id="emailError"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Enviar enlace de recuperación
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
        const form = document.getElementById('forgotPasswordForm');
        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');

        email.addEventListener('input', function() {
            clearError(this, emailError);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (email.value.trim() === '') {
                showError(email, emailError, 'El correo es obligatorio');
                return;
            }

            if (!isValidEmail(email.value)) {
                showError(email, emailError, 'Ingresa un correo válido (ejemplo@dominio.com)');
                return;
            }

            this.submit();
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
