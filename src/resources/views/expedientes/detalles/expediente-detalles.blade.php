@extends('layouts.app')

@section('title', 'Expediente ' . $expediente->numero_expediente)
@section('page-title', 'Expediente')

@section('content')
<div class="animate-fade-in">
    {{-- Header del Expediente --}}
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-sm font-semibold text-primary-400">{{ $expediente->numero_expediente }}</span>
                        @if($expediente->estado)
                            <span class="badge {{ $expediente->estado->badgeClass }}">
                                {{ $expediente->estado->estado }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold font-display text-text-primary">{{ $expediente->tipoProcedimiento->nombre ?? 'Expediente' }}</h1>
                </div>
            </div>

            @if(!$expediente->fecha_cierre)
                <form action="{{ route('expedientes.archivar', $expediente) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Archivar expediente
                    </button>
                </form>
            @else
                <div class="text-sm text-text-muted">
                    Archivado el {{ $expediente->fecha_cierre->format('d/m/Y') }}
                </div>
            @endif
        </div>

        {{-- Stats del expediente --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-primary-400/20 text-primary-400 flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Cliente</div>
                    <div class="text-sm font-medium text-text-primary">{{ $expediente->cliente->nombreCompleto ?? 'Sin asignar' }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-info/20 text-info flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Gestor</div>
                    <div class="text-sm font-medium text-text-primary">{{ $expediente->gestor->nombreCompleto ?? 'Sin asignar' }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-success/20 text-success flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Documentos</div>
                    <div class="text-sm font-medium text-text-primary">{{ $expediente->documentos->count() }} archivos</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl">
                <div class="w-12 h-12 rounded-xl bg-error/20 text-error flex items-center justify-center">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-text-muted uppercase tracking-wide mb-1">Tareas</div>
                    <div class="text-sm font-medium text-text-primary">{{ $expediente->tareas->whereNotIn('estado', ['completada', 'cancelada'])->count() }} pendientes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Progreso del Expediente (Automata de Estados) --}}
    @include('expedientes.detalles.partials.progreso-proceso')

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-white/5 pb-4 overflow-x-auto">
        <button type="button" class="tab-btn active" data-tab="documentos" onclick="switchExpedienteTab('documentos')">
            Documentos
        </button>
        <button type="button" class="tab-btn" data-tab="plan-pagos-honorarios" onclick="switchExpedienteTab('plan-pagos-honorarios')">
            Plan de Pagos Honorarios
        </button>
        <button type="button" class="tab-btn" data-tab="plan-pagos-acreedores" onclick="switchExpedienteTab('plan-pagos-acreedores')">
            Plan de Pagos Acreedores
        </button>
    </div>

    {{-- TAB DOCUMENTOS --}}
    @include('expedientes.detalles.tabs.documentos')

    {{-- TAB PLAN DE PAGOS HONORARIOS --}}
    @include('expedientes.detalles.tabs.plan-pagos-honorarios')

    {{-- TAB PLAN DE PAGOS ACREEDORES --}}
    @include('expedientes.detalles.tabs.plan-pagos-acreedores')
</div>

@push('scripts')
<script>
function switchExpedienteTab(tabName) {
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
