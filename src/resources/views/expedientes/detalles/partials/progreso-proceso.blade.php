{{-- Progreso del Proceso - Automata de Estados --}}
@if($expediente->tieneSeguimientoProceso())
<div class="section mb-6">
    <div class="flex items-center justify-between mb-1">
        <h2 class="text-xl font-semibold text-text-primary">Progreso del Expediente</h2>
        @if($expediente->estadoProceso && !$expediente->procesoFinalizado())
            <span class="badge {{ $expediente->estadoProceso->badgeClass }}">
                {{ $expediente->estadoProceso->nombre }}
            </span>
        @endif
    </div>
    <p class="text-sm text-text-secondary mb-6">Seguimiento de hitos y fechas clave del proceso</p>

    @if(!$expediente->estado_proceso_id)
        @if($expediente->fecha_cierre)
            {{-- Archivado antes de iniciar el proceso --}}
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-16 h-16 rounded-full bg-bg-light border border-white/10 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Expediente archivado</h3>
                <p class="text-sm text-text-secondary">El proceso nunca se inició. Archivado el {{ $expediente->fecha_cierre->format('d/m/Y') }}.</p>
            </div>
        @else
            {{-- Proceso no iniciado --}}
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-16 h-16 rounded-full bg-bg-light border-2 border-dashed border-primary-400/50 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Proceso no iniciado</h3>
                <p class="text-sm text-text-secondary mb-4">Inicia el seguimiento del proceso para este expediente</p>
                <form action="{{ route('expedientes.iniciar-proceso', $expediente) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Iniciar Proceso
                    </button>
                </form>
            </div>
        @endif
    @else
        {{-- Proceso iniciado - Mostrar timeline --}}
        @php
            $historial = $expediente->historialProceso->sortBy('fecha_entrada');
            $estadoActual = $expediente->estadoProceso;
            $transicionesDisponibles = $expediente->transicionesDisponibles;
        @endphp

        {{-- Timeline de estados completados y actual --}}
        <div class="flex items-start gap-4 overflow-x-auto pb-4 mb-6">
            @foreach($historial as $index => $registro)
                @php
                    $esActual = is_null($registro->fecha_salida);
                    $esCompletado = !is_null($registro->fecha_salida);
                    $esFinal = $registro->estado->es_final ?? false;
                @endphp

                {{-- Estado --}}
                <div class="flex flex-col items-center gap-2 min-w-[140px]">
                    @if($esCompletado)
                        {{-- Estado completado --}}
                        <div class="w-12 h-12 rounded-full bg-success flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-sm font-medium text-text-primary text-center">{{ $registro->estado->nombre }}</div>
                        <button type="button"
                                class="text-xs text-success hover:text-success/80 flex items-center gap-1 group"
                                onclick="mostrarModalEditarFecha({{ $registro->id }}, '{{ $registro->estado->nombre }}', '{{ $registro->fecha_entrada->format('Y-m-d') }}')">
                            {{ $registro->fecha_entrada->format('d/m/Y') }}
                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                    @elseif($esFinal)
                        {{-- Estado final --}}
                        <div class="w-12 h-12 rounded-full {{ $registro->estado->resultado_final === 'exito' ? 'bg-success' : 'bg-error' }} flex items-center justify-center text-white">
                            @if($registro->estado->resultado_final === 'exito')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="text-sm font-medium {{ $registro->estado->resultado_final === 'exito' ? 'text-success' : 'text-error' }} text-center">{{ $registro->estado->nombre }}</div>
                        <button type="button"
                                class="text-xs {{ $registro->estado->resultado_final === 'exito' ? 'text-success hover:text-success/80' : 'text-error hover:text-error/80' }} flex items-center gap-1 group"
                                onclick="mostrarModalEditarFecha({{ $registro->id }}, '{{ $registro->estado->nombre }}', '{{ $registro->fecha_entrada->format('Y-m-d') }}')">
                            {{ $registro->fecha_entrada->format('d/m/Y') }}
                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                    @else
                        {{-- Estado actual (en proceso) --}}
                        <div class="w-12 h-12 rounded-full bg-primary-400 flex items-center justify-center text-white animate-pulse">
                            @if($registro->estado->tipo_accion === 'espera_juzgado')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($registro->estado->tipo_accion === 'gestor')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="text-sm font-medium text-primary-400 text-center">{{ $registro->estado->nombre }}</div>
                        <button type="button"
                                class="text-xs text-primary-400 hover:text-primary-300 flex items-center gap-1 group"
                                onclick="mostrarModalEditarFecha({{ $registro->id }}, '{{ $registro->estado->nombre }}', '{{ $registro->fecha_entrada->format('Y-m-d') }}')">
                            {{ $registro->fecha_entrada->format('d/m/Y') }}
                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Linea conectora (excepto ultimo) --}}
                @if(!$loop->last)
                    @php
                        $siguienteRegistro = $historial->values()->get($loop->index + 1);
                        $siguienteEsActual = $siguienteRegistro && is_null($siguienteRegistro->fecha_salida);
                        // Gradiente si conecta estado completado con estado actual (en progreso)
                        $usarGradiente = $esCompletado && $siguienteEsActual;
                    @endphp
                    <div class="flex-1 h-1 mt-6 min-w-[30px] rounded-full {{ $usarGradiente ? 'bg-gradient-to-r from-success to-primary-400' : ($esCompletado ? 'bg-success' : 'bg-primary-400') }}"></div>
                @endif
            @endforeach

            {{-- Si hay transiciones disponibles, mostrar siguiente posible --}}
            @if($transicionesDisponibles->isNotEmpty() && !$expediente->procesoFinalizado())
                <div class="flex-1 h-1 mt-6 min-w-[30px] rounded-full bg-bg-light"></div>
                <div class="flex flex-col items-center gap-2 min-w-[140px]">
                    <div class="w-12 h-12 rounded-full bg-bg-light border-2 border-dashed border-bg-lighter flex items-center justify-center text-text-muted">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-text-muted text-center">Siguiente paso</div>
                    <div class="text-xs text-text-muted">Pendiente</div>
                </div>
            @endif
        </div>

        {{-- Acciones disponibles --}}
        @if(!$expediente->procesoFinalizado())
            @php
                $esEsperandoPublicaciones = $expediente->estadoProceso && $expediente->estadoProceso->codigo === 'esperando_publicaciones';
            @endphp

            @if($esEsperandoPublicaciones)
                {{-- Acciones especiales para registrar BOE/RPC --}}
                <div class="border-t border-white/5 pt-6">
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wide mb-4">Registrar Publicacion</h3>
                    <p class="text-sm text-text-muted mb-4">Registra la primera publicacion (BOE o RPC) para avanzar al periodo de alegaciones.</p>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" class="btn btn-primary" onclick="mostrarModalPublicacion('boe', 'BOE')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            Registrar BOE
                        </button>
                        <button type="button" class="btn btn-primary" onclick="mostrarModalPublicacion('rpc', 'RPC')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Registrar RPC
                        </button>
                    </div>
                </div>
            @elseif($transicionesDisponibles->isNotEmpty())
                {{-- Acciones normales del automata --}}
                <div class="border-t border-white/5 pt-6">
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wide mb-4">Acciones disponibles</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($transicionesDisponibles as $transicion)
                            <button type="button"
                                    class="btn {{ $transicion->es_principal ? 'btn-primary' : 'btn-secondary' }}"
                                    onclick="mostrarModalTransicion({{ $transicion->id }}, '{{ $transicion->etiqueta }}', '{{ $transicion->estadoDestino->nombre }}', {{ $transicion->requiere_confirmacion ? 'true' : 'false' }})">
                                @if($transicion->es_principal)
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                @endif
                                {{ $transicion->etiqueta }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Mensaje de proceso finalizado --}}
        @if($expediente->procesoFinalizado())
            <div class="border-t border-white/5 pt-6">
                @if($expediente->archivadoManualmente())
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-bg-medium border border-white/10">
                        <svg class="w-6 h-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span class="text-sm font-medium text-text-secondary">
                            Expediente archivado el {{ $expediente->fecha_cierre->format('d/m/Y') }}. El proceso quedó detenido en <strong class="text-text-primary">{{ $expediente->estadoProceso->nombre }}</strong>.
                        </span>
                    </div>
                @elseif($expediente->resultadoProceso === 'exito')
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-success/10 border border-success/30">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-success">Proceso completado con exito</span>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-error/10 border border-error/30">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-error">Proceso finalizado: {{ $expediente->estadoProceso->nombre }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Seccion de publicaciones pendientes (solo si ya paso el estado esperando_publicaciones) --}}
        @php
            $datosPublicaciones = $expediente->getDatosPublicaciones();
            $fechaBoe = $datosPublicaciones['fecha_publicacion_boe'] ?? null;
            $fechaRpc = $datosPublicaciones['fecha_publicacion_rpc'] ?? null;
            $tieneAlgunaPublicacion = $fechaBoe || $fechaRpc;
            $faltaAlgunaPublicacion = !$fechaBoe || !$fechaRpc;
            $estadoActualCodigo = $expediente->estadoProceso->codigo ?? null;

            // Mostrar seccion si ya paso el estado de publicaciones y falta registrar alguna
            $estadosPosteriorPublicaciones = ['periodo_alegaciones', 'sin_alegaciones', 'con_alegaciones',
                'resolucion_alegaciones', 'alegaciones_resueltas', 'presentar_solicitud_epi_plan', 'esperar_epi_provisional',
                'cumplimiento_plan', 'recurrir_modificar', 'esperar_resolucion_recurso', 'presentar_informe',
                'esperar_valoracion', 'solicitar_epi_definitivo', 'esperar_auto_definitivo', 'exoneracion_definitiva',
                'inadmitido', 'desestimado', 'revocacion_epi'];
            $mostrarSeccionPendientes = !$expediente->fecha_cierre && $tieneAlgunaPublicacion && $faltaAlgunaPublicacion && in_array($estadoActualCodigo, $estadosPosteriorPublicaciones);
        @endphp

        @if($mostrarSeccionPendientes)
            <div class="border-t border-white/5 pt-6 mt-6">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wide mb-4">Publicaciones Pendientes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($fechaBoe)
                        {{-- BOE ya registrado --}}
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-success/10 border border-success/30">
                            <div class="w-10 h-10 rounded-full bg-success flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-success">BOE</div>
                                <div class="text-xs text-success/80">Publicado el {{ $fechaBoe->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @else
                        {{-- BOE pendiente --}}
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-warning/10 border border-warning/30">
                            <div class="w-10 h-10 rounded-full bg-warning/20 flex items-center justify-center text-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-text-primary">BOE</div>
                                <div class="text-xs text-text-muted">Pendiente de registro</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" onclick="mostrarModalPublicacion('boe', 'BOE')">
                                Registrar
                            </button>
                        </div>
                    @endif

                    @if($fechaRpc)
                        {{-- RPC ya registrado --}}
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-success/10 border border-success/30">
                            <div class="w-10 h-10 rounded-full bg-success flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-success">RPC</div>
                                <div class="text-xs text-success/80">Publicado el {{ $fechaRpc->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @else
                        {{-- RPC pendiente --}}
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-warning/10 border border-warning/30">
                            <div class="w-10 h-10 rounded-full bg-warning/20 flex items-center justify-center text-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-text-primary">RPC</div>
                                <div class="text-xs text-text-muted">Pendiente de registro</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" onclick="mostrarModalPublicacion('rpc', 'RPC')">
                                Registrar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Modal para confirmar transicion --}}
<div id="modalTransicion" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalTransicion()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2" id="modalTitulo">Confirmar accion</h3>
            <p class="text-sm text-text-secondary mb-4" id="modalDescripcion">
                Vas a avanzar el proceso a un nuevo estado.
            </p>

            <form action="{{ route('expedientes.avanzar-proceso', $expediente) }}" method="POST" id="formTransicion">
                @csrf
                <input type="hidden" name="transicion_id" id="transicionId" value="">

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalTransicion()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para registrar publicacion BOE/RPC --}}
<div id="modalPublicacion" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalPublicacion()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Registrar Publicacion <span id="tipoPublicacionLabel"></span></h3>
            <p class="text-sm text-text-secondary mb-4">
                Indica la fecha en que se realizo la publicacion oficial.
            </p>

            <form action="{{ route('expedientes.registrar-publicacion', $expediente) }}" method="POST" id="formPublicacion">
                @csrf
                <input type="hidden" name="tipo" id="tipoPublicacion" value="">

                <div class="form-group">
                    <label for="fecha_publicacion" class="form-label">Fecha de publicacion</label>
                    <input type="date" id="fecha_publicacion" name="fecha" class="input" required value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalPublicacion()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para editar fecha del historial --}}
<div id="modalEditarFecha" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalEditarFecha()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Editar fecha de estado</h3>
            <p class="text-sm text-text-secondary mb-4">
                Modifica la fecha de entrada al estado: <span id="nombreEstadoEditar" class="font-medium text-text-primary"></span>
            </p>

            <form id="formEditarFecha" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="fecha_entrada_editar" class="form-label">Fecha</label>
                    <input type="date" id="fecha_entrada_editar" name="fecha_entrada" class="input" required>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalEditarFecha()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarModalTransicion(transicionId, etiqueta, estadoDestino, requiereConfirmacion) {
    document.getElementById('transicionId').value = transicionId;
    document.getElementById('modalTitulo').textContent = etiqueta;
    document.getElementById('modalDescripcion').textContent = 'El expediente pasara al estado: ' + estadoDestino;
    document.getElementById('modalTransicion').classList.remove('hidden');
}

function cerrarModalTransicion() {
    document.getElementById('modalTransicion').classList.add('hidden');
}

function mostrarModalPublicacion(tipo, label) {
    document.getElementById('tipoPublicacion').value = tipo;
    document.getElementById('tipoPublicacionLabel').textContent = label;
    document.getElementById('modalPublicacion').classList.remove('hidden');
}

function cerrarModalPublicacion() {
    document.getElementById('modalPublicacion').classList.add('hidden');
}

function mostrarModalEditarFecha(historialId, nombreEstado, fechaActual) {
    document.getElementById('nombreEstadoEditar').textContent = nombreEstado;
    document.getElementById('fecha_entrada_editar').value = fechaActual;
    document.getElementById('formEditarFecha').action = '/expedientes/{{ $expediente->id }}/historial/' + historialId;
    document.getElementById('modalEditarFecha').classList.remove('hidden');
}

function cerrarModalEditarFecha() {
    document.getElementById('modalEditarFecha').classList.add('hidden');
}

// Cerrar modales con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalTransicion();
        cerrarModalPublicacion();
        cerrarModalEditarFecha();
    }
});

// Prevenir doble envío de formularios
document.querySelectorAll('#formTransicion, #formPublicacion, #formEditarFecha').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }
        submitBtn.disabled = true;
        submitBtn.textContent = 'Procesando...';
    });
});
</script>
@endpush
@else
    {{-- Tipo de expediente sin seguimiento de proceso (ej: "Otros") --}}
    <div class="section mb-6">
        <h2 class="text-xl font-semibold text-text-primary mb-1">Progreso del Expediente</h2>
        <p class="text-sm text-text-secondary mb-6">Este tipo de expediente no tiene seguimiento de proceso automatizado</p>
        <div class="flex items-center gap-3 p-4 bg-bg-medium rounded-xl">
            <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm text-text-secondary">El seguimiento de hitos se gestiona manualmente para este tipo de procedimiento.</span>
        </div>
    </div>
@endif
