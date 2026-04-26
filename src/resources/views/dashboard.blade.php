@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen general del sistema')

@section('content')
    <div class="animate-fade-in">
        @php
            $fechaAnterior = $fechaSeleccionada->copy()->subDay()->toDateString();
            $fechaSiguiente = $fechaSeleccionada->copy()->addDay()->toDateString();
            $mesNombres = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
        @endphp

        {{-- Grid de 2 columnas: Alertas (2/3) + Calendario (1/3) --}}
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6 mb-6">
            {{-- Próximas Alertas --}}
            <div class="section">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard', ['fecha' => $fechaAnterior]) }}"
                           class="action-icon" title="Día anterior">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h3 class="section-title">
                            Alertas - {{ $fechaSeleccionada->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </h3>
                        <a href="{{ route('dashboard', ['fecha' => $fechaSiguiente]) }}"
                           class="action-icon" title="Día siguiente">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalRecordatorio()" title="Nuevo Recordatorio">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Nuevo Recordatorio</span>
                    </button>
                </div>

                @if($eventosDelDia->isEmpty())
                    <div class="empty-state">
                        <svg class="w-20 h-20 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h4 class="empty-state-title">No hay alertas para esta fecha</h4>
                        <p class="empty-state-description">Selecciona otra fecha o crea un nuevo recordatorio</p>
                    </div>
                @else
                    <div class="flex flex-col gap-3">
                        @foreach($eventosDelDia as $evento)
                            <x-alert-item
                                :tipo="$evento->tipo"
                                :day="$evento->fecha->day"
                                :month="$mesNombres[$evento->fecha->month - 1]"
                                :title="$evento->titulo"
                                :description="$evento->descripcion"
                                :reference="$evento->expediente?->numero_expediente"
                                :eventoId="$evento->id"
                            />
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Calendario Mini --}}
            <div class="section">
                <div class="section-header mb-5">
                    <h3 class="section-title">Calendario</h3>
                </div>

                <x-mini-calendar
                    :month="$fechaSeleccionada->month"
                    :year="$fechaSeleccionada->year"
                    :events="$eventosDelMes"
                    :selected-date="$fechaSeleccionada->toDateString()" />

                {{-- Leyenda --}}
                <div class="mt-4 p-4 bg-white/2 rounded-xl">
                    <div class="text-xs text-text-muted mb-2">Leyenda:</div>
                    <div class="flex flex-col gap-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-warning"></span>
                            <span class="text-text-secondary">Alertas</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-blue"></span>
                            <span class="text-text-secondary">Recordatorios</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Action Button (FAB) --}}
    <div class="fab-container group">
        <button class="fab-main" aria-label="Acciones rápidas">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>

        <div class="fab-menu">
            <a href="{{ route('clientes.create') }}" class="fab-item">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Añadir Cliente</span>
            </a>
            <a href="{{ route('expedientes.create') }}" class="fab-item">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Crear Expediente</span>
            </a>
            <button type="button" class="fab-item" onclick="abrirModalRecordatorio()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Nuevo Recordatorio</span>
            </button>
        </div>
    </div>

    @include('dashboard.partials.modal-recordatorio')
@endsection
