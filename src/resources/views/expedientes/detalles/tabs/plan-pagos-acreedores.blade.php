<div id="tab-plan-pagos-acreedores" class="tab-content">
    <div class="section">
        @php
            $lineas = $expediente->planPagosAcreedores;
            $deudaTotal = $lineas->sum('deuda_original');
            $propuestaTotal = $lineas->sum('propuesta');
            $pagado = $lineas->where('pagado', true)->sum('propuesta');
            $pendiente = max(0, $propuestaTotal - $pagado);
            $quita = max(0, $deudaTotal - $propuestaTotal);
            $porcentajeQuita = $deudaTotal > 0 ? round(($quita / $deudaTotal) * 100) : 0;
        @endphp

        <div class="section-header">
            <h3 class="section-title">Plan de Pagos - Acreedores</h3>
            <button type="button" class="btn btn-primary" onclick="abrirModalAnadirAcreedor()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Añadir Acreedor
            </button>
        </div>

        @if($lineas->isEmpty())
            <div class="empty-state">
                <svg class="w-20 h-20 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m3-4h10a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2v-6a2 2 0 012-2zm7 5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h4 class="empty-state-title">No hay acreedores en el plan</h4>
                <p class="empty-state-description">Añade acreedores al plan indicando su nombre, la deuda original y la cantidad propuesta a pagar.</p>
            </div>
        @else
            {{-- Resumen --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Deuda Total</div>
                    <div class="text-3xl font-bold text-text-primary">{{ number_format($deudaTotal, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ $lineas->count() }} acreedor{{ $lineas->count() === 1 ? '' : 'es' }}</div>
                </div>

                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Propuesta</div>
                    <div class="text-3xl font-bold text-warning">{{ number_format($propuestaTotal, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ 100 - $porcentajeQuita }}% del total</div>
                </div>

                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Pagado</div>
                    <div class="text-3xl font-bold text-success">{{ number_format($pagado, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ number_format($pendiente, 2, ',', '.') }} EUR pendiente</div>
                </div>

                <div class="p-5 bg-bg-medium rounded-xl border border-white/5">
                    <div class="text-sm text-text-secondary mb-2">Quita</div>
                    <div class="text-3xl font-bold text-info">{{ number_format($quita, 2, ',', '.') }} EUR</div>
                    <div class="text-xs text-text-muted mt-1">{{ $porcentajeQuita }}% condonado</div>
                </div>
            </div>

            {{-- Tabla de acreedores --}}
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Acreedor</th>
                            <th>Deuda Original</th>
                            <th>Propuesta</th>
                            <th>Estado</th>
                            <th>Fecha Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lineas as $linea)
                            <tr>
                                <td><strong class="text-text-primary">{{ $linea->nombre_acreedor }}</strong></td>
                                <td class="font-semibold text-text-primary">{{ number_format($linea->deuda_original, 2, ',', '.') }} EUR</td>
                                <td class="font-semibold text-warning">{{ number_format($linea->propuesta, 2, ',', '.') }} EUR</td>
                                <td>
                                    @if($linea->pagado)
                                        <span class="badge badge-activo">Pagado</span>
                                    @else
                                        <span class="badge badge-pendiente">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-text-secondary">{{ $linea->fecha_pago?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    @if(!$linea->pagado)
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="abrirModalRegistrarPagoAcreedor({{ $linea->id }}, '{{ addslashes($linea->nombre_acreedor) }}', {{ $linea->propuesta }})">
                                                Registrar pago
                                            </button>
                                            <button type="button" class="action-icon" title="Editar importes"
                                                    onclick="abrirModalEditarAcreedor({{ $linea->id }}, '{{ addslashes($linea->nombre_acreedor) }}', {{ $linea->deuda_original }}, {{ $linea->propuesta }})">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('expedientes.detalles.partials.modal-anadir-acreedor', ['expediente' => $expediente])
    @include('expedientes.detalles.partials.modal-editar-acreedor', ['expediente' => $expediente])
    @include('expedientes.detalles.partials.modal-registrar-pago-acreedor', ['expediente' => $expediente])
</div>
