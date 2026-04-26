@foreach ($expedientes as $expediente)
    <x-expediente-card
        :id="$expediente->id"
        :code="$expediente->numero_expediente"
        :status-class="$expediente->estado->badgeClass ?? 'badge-proceso'"
        :status-label="$expediente->estado->estado ?? 'Sin estado'"
        :title="$expediente->tipoProcedimiento->nombre ?? 'Sin tipo de procedimiento'"
        :client-name="$expediente->cliente->nombreCompleto ?? 'Sin cliente'"
        :lawyer-name="$expediente->gestor->nombreCompleto ?? 'Sin gestor'"
        :documents="$expediente->documentos_count ?? $expediente->documentos->count()"
    />
@endforeach
