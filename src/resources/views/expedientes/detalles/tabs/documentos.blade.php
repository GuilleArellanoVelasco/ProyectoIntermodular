<div id="tab-documentos" class="tab-content active">
    <div class="section">
        <div class="section-header">
            <h3 class="section-title">Documentos del Expediente</h3>
            <button type="button" onclick="abrirModalSubirDocumento()" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Subir Documento
            </button>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Tamano</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expediente->documentos as $documento)
                        @php
                            $fileType = 'file';
                            if (Str::contains($documento->mime_type ?? '', 'pdf')) {
                                $fileType = 'pdf';
                            } elseif (Str::contains($documento->mime_type ?? '', ['word', 'document'])) {
                                $fileType = 'word';
                            } elseif (Str::contains($documento->mime_type ?? '', ['excel', 'spreadsheet'])) {
                                $fileType = 'excel';
                            }

                            $iconColor = match($fileType) {
                                'pdf' => 'bg-error/20 text-error',
                                'word' => 'bg-info/20 text-info',
                                'excel' => 'bg-success/20 text-success',
                                default => 'bg-text-muted/20 text-text-muted'
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-text-primary truncate" title="{{ $documento->nombre }}">{{ $documento->nombre }}</span>
                                </div>
                            </td>
                            <td class="text-text-secondary">{{ $documento->tipoDocumento->nombre ?? 'Sin categoria' }}</td>
                            <td>
                                <span class="uppercase text-xs font-semibold text-text-muted">{{ $fileType }}</span>
                            </td>
                            <td class="text-text-secondary">{{ $documento->tamanio_formateado }}</td>
                            <td class="text-text-secondary">{{ $documento->created_at?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('documentos.download', $documento) }}" class="action-icon" title="Descargar">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('documentos.view', $documento) }}" target="_blank" class="action-icon" title="Ver">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('documentos.destroy', $documento) }}"
                                          onsubmit="return confirm('¿Eliminar este documento? Esta acción no se puede deshacer.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon" title="Eliminar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-bg-light flex items-center justify-center">
                                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-text-muted">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-text-muted">No hay documentos registrados para este expediente</p>
                                    <button type="button" onclick="abrirModalSubirDocumento()" class="btn btn-primary">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Subir primer documento
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('documentos.partials.modal-subir', ['contextoTipo' => 'expediente', 'contextoId' => $expediente->id])
