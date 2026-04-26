<aside class="fixed top-0 left-0 w-[72px] xl:w-[240px] h-screen bg-gradient-to-b from-bg-dark to-bg-darkest border-r border-primary-400/10 p-2 xl:p-6 flex flex-col z-[100] transition-[width] duration-200">
    <div class="mb-8 pb-6 border-b border-white/5 flex flex-col items-center xl:items-start">
        <h1 class="font-display font-bold text-primary-400 mb-1 text-2xl">
            <span class="text-primary-400">L</span><span class="hidden xl:inline text-primary-400">iber</span><span class="hidden xl:inline text-text-primary">xo</span>
        </h1>
        <p class="hidden xl:block text-xs text-text-secondary">Gestión de Clientes y Expedientes</p>
    </div>

    <nav class="flex-1 flex flex-col gap-2">
        <a href="{{ route('dashboard') }}"
           title="Dashboard"
           class="menu-item justify-center xl:justify-start gap-0 xl:gap-3 px-2 xl:px-4 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="hidden xl:inline">Dashboard</span>
        </a>

        <a href="{{ route('clientes') }}"
           title="Clientes"
           class="menu-item justify-center xl:justify-start gap-0 xl:gap-3 px-2 xl:px-4 {{ request()->routeIs(['clientes', 'clientes.*']) ? 'active' : '' }}">
            <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="hidden xl:inline">Clientes</span>
        </a>

        <a href="{{ route('expedientes') }}"
           title="Expedientes"
           class="menu-item justify-center xl:justify-start gap-0 xl:gap-3 px-2 xl:px-4 {{ request()->routeIs(['expedientes', 'expedientes.*']) ? 'active' : '' }}">
            <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            <span class="hidden xl:inline">Expedientes</span>
        </a>

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('usuarios') }}"
               title="Usuarios"
               class="menu-item justify-center xl:justify-start gap-0 xl:gap-3 px-2 xl:px-4 {{ request()->routeIs(['usuarios', 'usuarios.*']) ? 'active' : '' }}">
                <svg class="shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="hidden xl:inline">Usuarios</span>
            </a>
        @endif
    </nav>
</aside>
