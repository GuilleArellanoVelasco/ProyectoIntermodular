<div id="tab-expedientes" class="tab-content">
    <div class="section">
        <div class="section-header">
            <h3 class="section-title">Todos los Expedientes</h3>
            <a href="{{ route('expedientes.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Expediente
            </a>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($cliente->expedientes as $expediente)
            <div class="card-hover">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-sm font-semibold text-primary-400 mb-1">{{ $expediente->numero_expediente }}</div>
                        <h3 class="text-lg font-semibold text-text-primary">{{ $expediente->tipoProcedimiento->nombre ?? 'Sin tipo de procedimiento' }}</h3>
                    </div>
                    @if($expediente->estado)
                        <span class="badge {{ $expediente->estado->badgeClass }}">{{ $expediente->estado->estado }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-6 text-sm text-text-secondary mb-4">
                    @if($expediente->gestor)
                    <div>
                        <span class="text-text-muted">Gestor:</span>
                        <span class="text-text-primary ml-1">{{ $expediente->gestor->nombre_completo }}</span>
                    </div>
                    @endif
                    @if($expediente->fecha_apertura)
                    <div>
                        <span class="text-text-muted">Apertura:</span>
                        <span class="text-text-primary ml-1">{{ $expediente->fecha_apertura->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-white/5">
                    <a href="{{ route('expedientes.show', $expediente->id) }}" class="link text-sm font-medium">Ver detalles &rarr;</a>
                </div>
            </div>
            @empty
            <div class="p-8 bg-bg-medium rounded-xl text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-bg-light flex items-center justify-center">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-text-muted">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <p class="text-text-muted mb-4">No hay expedientes registrados para este cliente</p>
                <a href="{{ route('expedientes.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear primer expediente
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
