<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Liberxo') - Sistema de Gestión</title>

    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body>
    <div class="flex min-h-screen bg-bg-darkest">
        @include('components.sidebar')

        <div class="flex-1 min-w-0 ml-[72px] xl:ml-[240px] min-h-screen transition-[margin] duration-200">
            @include('components.navbar')

            {{-- Flash Messages / Toasts --}}
            @if(session('success') || session('error'))
            <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3">
                @if(session('success'))
                <div class="toast toast-success animate-slide-in-right" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="toast-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="toast-message">{{ session('success') }}</p>
                        <button type="button" class="toast-close" onclick="this.closest('.toast').remove()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="toast toast-error animate-slide-in-right" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="toast-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="toast-message">{{ session('error') }}</p>
                        <button type="button" class="toast-close" onclick="this.closest('.toast').remove()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <main class="p-8 max-w-[1400px] mx-auto">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Auto-dismiss toasts --}}
    @if(session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('animate-slide-out-right');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            });
        });
    </script>
    @endif

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
