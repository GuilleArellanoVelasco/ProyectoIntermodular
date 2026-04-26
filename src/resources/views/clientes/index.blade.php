@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', 'Gestiona todos tus clientes')

@section('content')
    <div class="animate-fade-in">
        <div class="page-header">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-0">
                <form action="{{ route('clientes') }}" method="GET" class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" class="search-input"
                        placeholder="Buscar por nombre, DNI o email..."
                        value="{{ request('search') }}">
                </form>

                {{-- Botón de ordenar --}}
                @php
                    $orderOptions = [
                        'fecha' => 'Fecha',
                        'nombre' => 'Nombre',
                        'expedientes' => 'Expedientes',
                    ];
                    $currentOrder = request('order', 'fecha');
                    $currentDir = request('dir', 'desc');
                @endphp

                <div class="sort-control">
                    {{-- Dropdown para seleccionar campo --}}
                    <div class="sort-dropdown" data-name="order">
                        <button type="button" class="sort-dropdown-trigger">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9" />
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

                    {{-- Botón toggle para dirección --}}
                    <button type="button" class="sort-direction-toggle {{ $currentDir }}"
                            data-dir="{{ $currentDir }}"
                            title="{{ $currentDir === 'asc' ? 'Ascendente' : 'Descendente' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @php
                    $estadosOptions = [
                        ['value' => 'todos', 'label' => 'Todos los estados'],
                        ['value' => 'activo', 'label' => 'Activo'],
                        ['value' => 'inactivo', 'label' => 'Inactivo'],
                    ];
                @endphp

                <x-custom-select name="estado" :options="$estadosOptions" :selected="request('estado', 'todos')" :redirect="true" />

                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Añadir Cliente
                </a>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h3 class="section-title">{{ $clientes->total() }} {{ Str::plural('Cliente', $clientes->total()) }}</h3>
                <a href="{{ route('clientes.export', request()->query()) }}" class="btn btn-ghost btn-sm">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Exportar
                </a>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>NºDocumento</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Expedientes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clients-tbody">
                        @forelse ($clientes as $cliente)
                            <x-client-item
                                :id="$cliente->id"
                                :name="$cliente->nombre_completo"
                                :initials="$cliente->iniciales"
                                :avatar-color="$cliente->avatar_color"
                                :dni="$cliente->numero_documentacion"
                                :phone="$cliente->telefono ?? 'Sin teléfono'"
                                :email="$cliente->email ?? 'Sin email'"
                                :status="$cliente->estado_slug"
                                :cases-count="$cliente->expedientes_count"
                            />
                        @empty
                            <tr id="empty-state">
                                <td colspan="6" class="text-center py-8 opacity-60">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="opacity-40">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p>No hay clientes registrados</p>
                                        <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm mt-2">
                                            Crear primer cliente
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Ver más --}}
            <div id="load-more-container" class="mt-6 flex justify-center {{ $clientes->hasMorePages() ? '' : 'hidden' }}">
                <button type="button" id="load-more-btn" class="link text-sm" data-page="{{ $clientes->currentPage() + 1 }}">
                    Ver más
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script type="module">
    ListUtils.initListPage('clients-tbody', 'clientes');
</script>
@endpush
@endsection
