<div id="tab-resumen" class="tab-content active">
    {{-- Informacion del Cliente --}}
    <div class="section">
        <h3 class="section-title">Informacion del Cliente</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="detail-item">
                <div class="detail-item-icon bg-primary-400/20 text-primary-400">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Direccion</div>
                    <div class="detail-item-value">{{ $cliente->direccion ?? 'Sin direccion' }}</div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-item-icon bg-info/20 text-info">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Expedientes Activos</div>
                    @php
                        $countActivos = $cliente->expedientes->filter(function($exp) {
                            return !$exp->estado || Str::slug($exp->estado->estado) !== 'archivado';
                        })->count();
                    @endphp
                    <div class="detail-item-value">{{ $countActivos }} expedientes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informacion de la Empresa --}}
    @if($cliente->empresa)
    <div class="section">
        <h3 class="section-title">Informacion de la Empresa</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="detail-item">
                <div class="detail-item-icon bg-secondary/20 text-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Nombre</div>
                    <div class="detail-item-value">{{ $cliente->empresa->nombre }}</div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-item-icon bg-primary-400/20 text-primary-400">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">CIF</div>
                    <div class="detail-item-value">{{ $cliente->empresa->cif }}</div>
                </div>
            </div>

            @if($cliente->empresa->email)
            <div class="detail-item">
                <div class="detail-item-icon bg-info/20 text-info">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Email</div>
                    <div class="detail-item-value">{{ $cliente->empresa->email }}</div>
                </div>
            </div>
            @endif

            @if($cliente->empresa->telefono)
            <div class="detail-item">
                <div class="detail-item-icon bg-success/20 text-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Telefono</div>
                    <div class="detail-item-value">{{ $cliente->empresa->telefono }}</div>
                </div>
            </div>
            @endif

            @if($cliente->empresa->direccion)
            <div class="detail-item md:col-span-2">
                <div class="detail-item-icon bg-primary-400/20 text-primary-400">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Direccion</div>
                    <div class="detail-item-value">{{ $cliente->empresa->direccion }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Informacion del Consorte --}}
    @if($cliente->consorte)
    <div class="section">
        <h3 class="section-title">Informacion del Consorte</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="detail-item">
                <div class="detail-item-icon bg-secondary/20 text-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Nombre Completo</div>
                    <div class="detail-item-value">{{ $cliente->consorte->nombre_completo }}</div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-item-icon bg-primary-400/20 text-primary-400">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">{{ $cliente->consorte->tipoDocumentacion->nombre ?? 'Documento' }}</div>
                    <div class="detail-item-value">{{ $cliente->consorte->numero_documentacion }}</div>
                </div>
            </div>

            @if($cliente->consorte->email)
            <div class="detail-item">
                <div class="detail-item-icon bg-info/20 text-info">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Email</div>
                    <div class="detail-item-value">{{ $cliente->consorte->email }}</div>
                </div>
            </div>
            @endif

            @if($cliente->consorte->telefono)
            <div class="detail-item">
                <div class="detail-item-icon bg-success/20 text-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <div class="detail-item-label">Telefono</div>
                    <div class="detail-item-value">{{ $cliente->consorte->telefono }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Grid de 2 columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Expedientes Activos --}}
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">Expedientes Activos</h3>
            </div>

            @php
                $expedientesActivos = $cliente->expedientes->filter(function($expediente) {
                    return !$expediente->estado || $expediente->estado->estado !== 'Archivado';
                });
            @endphp
            <div class="flex flex-col gap-3">
                @forelse($expedientesActivos as $expediente)
                <a href="{{ route('expedientes.show', $expediente) }}" class="flex items-center justify-between p-4 bg-bg-medium rounded-xl hover:bg-bg-light transition-colors">
                    <div>
                        <div class="text-xs text-primary-400 font-semibold mb-1">{{ $expediente->numero_expediente }}</div>
                        <div class="text-sm text-text-primary">{{ $expediente->tipoProcedimiento->nombre ?? 'Sin tipo' }}</div>
                    </div>
                    @if($expediente->estado)
                        <span class="badge {{ $expediente->estado->badgeClass }}">{{ $expediente->estado->estado }}</span>
                    @endif
                </a>
                @empty
                <div class="p-4 bg-bg-medium rounded-xl text-center">
                    <p class="text-text-muted text-sm">No hay expedientes activos</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Documentos Recientes --}}
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">Documentos Recientes</h3>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($cliente->documentos->take(3) as $documento)
                @php
                    $fileType = 'file';
                    if (Str::contains($documento->mime_type ?? '', 'pdf')) {
                        $fileType = 'pdf';
                    } elseif (Str::contains($documento->mime_type ?? '', ['word', 'document'])) {
                        $fileType = 'word';
                    } elseif (Str::contains($documento->mime_type ?? '', ['excel', 'spreadsheet'])) {
                        $fileType = 'excel';
                    } elseif (Str::contains($documento->mime_type ?? '', 'image')) {
                        $fileType = 'image';
                    }

                    $iconColor = match($fileType) {
                        'pdf' => 'bg-error/20 text-error',
                        'word' => 'bg-info/20 text-info',
                        'excel' => 'bg-success/20 text-success',
                        'image' => 'bg-accent-purple/20 text-accent-purple',
                        default => 'bg-text-muted/20 text-text-muted'
                    };
                @endphp
                <div class="flex items-center gap-4 p-4 bg-bg-medium rounded-xl hover:bg-bg-light transition-colors">
                    <div class="w-10 h-10 rounded-lg {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-text-primary truncate">{{ $documento->nombre }}</div>
                        <div class="text-xs text-text-muted">{{ $documento->tipoDocumento->nombre ?? 'Documento' }} - {{ $documento->tamanio_formateado }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 bg-bg-medium rounded-xl text-center">
                    <p class="text-text-muted text-sm">No hay documentos recientes</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
