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

    @include('dashboard.partials.modal-recordatorio')
@endsection
