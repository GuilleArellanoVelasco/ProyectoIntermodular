<div id="tab-plan-pagos-honorarios" class="tab-content">
    <div class="section">
        @php
            $plan = $expediente->planPagoHonorarios;
            $cuotas = $plan?->cuotas ?? collect();
            $totalPagado = $cuotas->where('pagada', true)->sum('importe');
            $totalPlan = $plan?->importe_total ?? 0;
            $pendiente = max(0, $totalPlan - $totalPagado);
            $porcentajePagado = $totalPlan > 0 ? round(($totalPagado / $totalPlan) * 100) : 0;
            $siguienteCuota = $cuotas->firstWhere('pagada', false);
        @endphp

        <div class="section-header">
            <h3 class="section-title">Plan de Pagos - Honorarios Profesionales</h3>
            @if(!$plan)
                <button type="button" class="btn btn-primary" onclick="abrirModalCrearPlanHonorarios()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Plan de Pagos
                </button>
            @elseif($siguienteCuota)
                <button type="button" class="btn btn-primary" onclick="abrirModalRegistrarPagoHonorarios()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar Pago
                </button>
            @else
                <span class="badge badge-activo">Plan completado</span>
            @endif
        </div>

        @if(!$plan)
            <div class="empty-state">
                <svg class="w-20 h-20 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h4 class="empty-state-title">No hay plan de pagos</h4>
                <p class="empty-state-description">Crea un plan indicando el importe total, el número de cuotas y la fecha del primer vencimiento.</p>
            </div>
        @else
            {{-- Resumen --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Total Honorarios</div>
                    <div class="text-3xl font-bold text-text-primary">{{ number_format($totalPlan, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ $plan->numero_cuotas }} cuotas de {{ number_format($plan->importe_cuota, 2, ',', '.') }} EUR</div>
                </div>

                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Pagado</div>
                    <div class="text-3xl font-bold text-success">{{ number_format($totalPagado, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ $porcentajePagado }}% completado</div>
                </div>

                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Pendiente</div>
                    <div class="text-3xl font-bold text-warning">{{ number_format($pendiente, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ 100 - $porcentajePagado }}% restante</div>
                </div>
            </div>

            {{-- Tabla de cuotas --}}
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N Cuota</th>
                            <th>Fecha Vencimiento</th>
                            <th>Importe</th>
                            <th>Estado</th>
                            <th>Fecha Pago</th>
                            <th>Método</th>
                            <th>Factura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuotas as $cuota)
                            <tr>
                                <td><strong>{{ $cuota->numero_cuota }}/{{ $plan->numero_cuotas }}</strong></td>
                                <td class="text-text-secondary">{{ $cuota->fecha_vencimiento?->format('d/m/Y') }}</td>
                                <td class="font-semibold text-text-primary">{{ number_format($cuota->importe, 2, ',', '.') }} EUR</td>
                                <td>
                                    @if($cuota->pagada)
                                        <span class="badge badge-activo">Pagada</span>
                                    @else
                                        <span class="badge badge-pendiente">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-text-secondary">{{ $cuota->fecha_pago?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-text-secondary">
                                    @if($cuota->metodo_pago)
                                        <span class="capitalize">{{ $cuota->metodo_pago }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($cuota->factura)
                                        @if($cuota->factura->tiene_pdf)
                                            <a href="{{ route('facturas-honorarios.descargar', $cuota->factura) }}"
                                               class="font-mono text-sm text-primary-400 hover:text-primary-300 inline-flex items-center gap-1 whitespace-nowrap"
                                               title="Descargar factura">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                {{ $cuota->factura->numero_factura }}
                                            </a>
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-secondary inline-flex items-center gap-1"
                                                    onclick="abrirModalSubirFactura({{ $cuota->factura->id }}, '{{ $cuota->factura->numero_factura }}')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                Subir factura
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if(!$plan)
        @include('expedientes.detalles.partials.modal-crear-plan-honorarios', ['expediente' => $expediente])
    @elseif($siguienteCuota)
        @include('expedientes.detalles.partials.modal-registrar-pago-honorarios', [
            'expediente' => $expediente,
            'plan' => $plan,
            'siguienteCuota' => $siguienteCuota,
        ])
    @endif

    @if($plan)
        @include('expedientes.detalles.partials.modal-subir-factura')
    @endif
</div>
