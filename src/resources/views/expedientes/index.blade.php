@extends('layouts.app')

@section('title', 'Expedientes')
@section('page-title', 'Expedientes')
@section('page-subtitle', 'Gestiona todos los expedientes')

@section('content')
<div class="animate-fade-in">
    <div class="page-header">
        <div class="flex flex-wrap items-center gap-3 flex-1 min-w-0">
            <form action="{{ route('expedientes') }}" method="GET" class="search-bar">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" class="search-input" placeholder="Buscar por cliente, gestor o EXP-..." value="{{ request('search') }}">
            </form>

            {{-- Botón de ordenar --}}
            @php
                $orderOptions = [
                    'fecha' => 'Fecha',
                ];
                $currentOrder = request('order', 'fecha');
                $currentDir = request('dir', 'desc');
            @endphp

            <div class="sort-control">
                <div class="sort-dropdown" data-name="order">
                    <button type="button" class="sort-dropdown-trigger">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9"/>
                        </svg>
                        <span>{{ $orderOptions[$currentOrder] ?? 'Fecha' }}</span>
                        <svg class="sort-dropdown-arrow" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="sort-dropdown-menu">
                        @foreach($orderOptions as $value => $label)
                            <div class="sort-dropdown-item {{ $currentOrder === $value ? 'active' : '' }}"
                                 data-value="{{ $value }}">
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="sort-direction-toggle {{ $currentDir }}"
                        data-dir="{{ $currentDir }}"
                        title="{{ $currentDir === 'asc' ? 'Ascendente' : 'Descendente' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            @php
                $estadosOptions = [
                    ['value' => 'todos', 'label' => 'Todos los estados'],
                    ['value' => 'abiertos', 'label' => 'Abiertos'],
                    ['value' => 'pendiente_accion', 'label' => 'Pendientes de acción'],
                    ['value' => 'archivados', 'label' => 'Archivados'],
                ];

                $tiposOptions = [
                    ['value' => 'todos', 'label' => 'Todos los tipos'],
                ];
                foreach($tiposProcedimiento as $tipo) {
                    $tiposOptions[] = ['value' => (string) $tipo->id, 'label' => $tipo->nombre];
                }
            @endphp

            <x-custom-select
                name="estado"
                :options="$estadosOptions"
                :selected="request('estado', 'todos')"
                :redirect="true"
            />

            <x-custom-select
                name="tipo"
                :options="$tiposOptions"
                :selected="request('tipo', 'todos')"
                :redirect="true"
            />

            <a href="{{ route('expedientes.create') }}" class="btn btn-primary">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Expediente
            </a>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h3 class="section-title">{{ $expedientes->total() }} {{ Str::plural('Expediente', $expedientes->total()) }}</h3>
            <a href="{{ route('expedientes.export', request()->query()) }}" class="btn btn-ghost btn-sm">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Exportar
            </a>
        </div>

        {{-- Grid de expedientes --}}
        <div class="cards-grid" id="expedientes-grid">
            @forelse ($expedientes as $expediente)
                <x-expediente-card
                    :id="$expediente->id"
                    :code="$expediente->numero_expediente"
                    :status-class="$expediente->estado->badgeClass ?? 'badge-proceso'"
                    :status-label="$expediente->estado->estado ?? 'Sin estado'"
                    :title="$expediente->tipoProcedimiento->nombre ?? 'Sin tipo de procedimiento'"
                    :client-name="$expediente->cliente->nombreCompleto ?? 'Sin cliente'"
                    :lawyer-name="$expediente->gestor->nombreCompleto ?? 'Sin gestor'"
                    :documents="$expediente->documentos_count"
                />
            @empty
                <div class="col-span-full" id="empty-state">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-20 h-20 rounded-full bg-bg-medium flex items-center justify-center mb-6">
                            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-text-muted">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-text-primary mb-2">No hay expedientes</h3>
                        <p class="text-text-muted mb-6">Crea tu primer expediente para comenzar</p>
                        <a href="{{ route('expedientes.create') }}" class="btn btn-primary">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Crear Expediente
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Ver más --}}
        <div id="load-more-container" class="mt-6 flex justify-center {{ $expedientes->hasMorePages() ? '' : 'hidden' }}">
            <button type="button" id="load-more-btn" class="link text-sm" data-page="{{ $expedientes->currentPage() + 1 }}">
                Ver mas
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    ListUtils.initListPage('expedientes-grid', 'expedientes');
</script>
@endpush
@endsection
