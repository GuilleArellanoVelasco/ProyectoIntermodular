@extends('layouts.app')

@section('title', 'Cliente')
@section('page-title', 'Cliente')

@section('content')
<div class="animate-fade-in">
    {{-- Header del Cliente --}}
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl {{ $cliente->avatar_color }} flex items-center justify-center text-white text-2xl font-bold">
                    {{ $cliente->iniciales }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold font-display text-text-primary">{{ $cliente->nombre ?? 'Maria Gonzalez Perez' }}</h1>
                    <p class="text-text-secondary">DNI: {{ $cliente->numero_documentacion ?? '12345678A' }}</p>
                </div>
            </div>

            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Cliente
            </a>
        </div>

        {{-- Stats del cliente --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl min-w-0">
                <div class="w-12 h-12 rounded-xl bg-primary-400/20 text-primary-400 flex items-center justify-center flex-shrink-0">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Email</div>
                    <div class="text-sm font-medium text-text-primary truncate" title="{{ $cliente->email ?? 'maria.gonzalez@email.com' }}">{{ $cliente->email ?? 'maria.gonzalez@email.com' }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-info/20 text-info flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Telefono</div>
                    <div class="text-sm font-medium text-text-primary">{{ $cliente->telefono ?? '' }}</div>
                </div>
            </div>

            @php
                $estadoCliente = $cliente->estado;
                $estadoColor = $cliente->esta_activo
                    ? ['bg' => 'bg-success/20', 'text' => 'text-success']
                    : ['bg' => 'bg-text-muted/20', 'text' => 'text-text-muted'];
            @endphp
            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl {{ $estadoColor['bg'] }} {{ $estadoColor['text'] }} flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Estado</div>
                    <div class="text-sm font-medium text-text-primary">{{ $estadoCliente }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-accent-purple/20 text-accent-purple flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Registrado</div>
                    <div class="text-sm font-medium text-text-primary">{{ $cliente->created_at?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-white/5 pb-4">
        <button type="button" class="tab-btn active" data-tab="resumen" onclick="switchClientTab('resumen')">
            Resumen
        </button>
        <button type="button" class="tab-btn" data-tab="expedientes" onclick="switchClientTab('expedientes')">
            Expedientes
        </button>
        <button type="button" class="tab-btn" data-tab="documentos" onclick="switchClientTab('documentos')">
            Documentos
        </button>
    </div>

    {{-- TAB RESUMEN --}}
    @include('clientes.detalles.tabs.resumen')

    {{-- TAB EXPEDIENTES --}}
    @include('clientes.detalles.tabs.expedientes')

    {{-- TAB DOCUMENTOS --}}
    @include('clientes.detalles.tabs.documentos')
</div>

@push('scripts')
<script>
function switchClientTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(button => {
        button.classList.remove('active');
    });

    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}
</script>
@endpush
@endsection
