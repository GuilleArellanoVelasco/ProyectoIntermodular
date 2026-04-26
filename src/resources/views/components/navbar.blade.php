<header class="flex justify-between items-center p-6 bg-bg-dark/50 border-b border-white/5">
    <div>
        <h2 class="text-2xl font-bold font-display text-text-primary mb-1">@yield('page-title', 'Dashboard')</h2>
        <p class="text-sm text-text-secondary">@yield('page-subtitle', 'Resumen general del sistema')</p>
    </div>

    @php
        $user = Auth::user();
        $userRole = $user->roles->first()?->name ?? 'Usuario';
        // Capitalizar primera letra del rol
        $userRoleDisplay = ucfirst($userRole);
    @endphp

    <div class="relative">
        <button class="flex items-center gap-4 py-2 px-3 bg-white/2 border border-white/5 rounded-xl cursor-pointer transition-all duration-200 hover:bg-white/5 hover:border-primary-400/30 group" id="userMenuButton">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center font-bold text-sm text-white shrink-0">
                {{ $user->iniciales }}
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-text-primary">{{ $user->nombreCompleto }}</div>
                <div class="text-xs text-text-secondary">{{ $userRoleDisplay }}</div>
            </div>
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-text-muted transition-transform duration-200 group-hover:rotate-180">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        @php
            $userMenuItems = [
                [
                    'type' => 'link',
                    'url' => route('perfil'),
                    'label' => 'Mi Perfil',
                    'icon' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>'
                ],
                [
                    'type' => 'form',
                    'action' => route('logout'),
                    'label' => 'Cerrar Sesión',
                    'class' => 'danger',
                    'icon' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>'
                ]
            ];
        @endphp

        <x-dropdown-menu
            id="userDropdown"
            :items="$userMenuItems"
            position="right"
            min-width="220px"
        />
    </div>
</header>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenuButton.classList.toggle('active');
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userMenuButton.classList.remove('active');
                userDropdown.classList.remove('show');
            }
        });

        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
@endpush
@endonce
