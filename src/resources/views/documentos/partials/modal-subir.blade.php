{{-- Modal para subir un documento --}}
{{-- Requiere variables: $contextoTipo ('cliente'|'expediente'), $contextoId, $tiposDocumento --}}
<div id="modalSubirDocumento" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalSubirDocumento()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Subir Documento</h3>
            <p class="text-sm text-text-secondary mb-4">
                Formatos permitidos: PDF, Word (.doc, .docx) y Excel (.xls, .xlsx). Tamaño máximo 10 MB.
            </p>

            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" id="formSubirDocumento">
                @csrf
                <input type="hidden" name="contexto_tipo" value="{{ $contextoTipo }}">
                <input type="hidden" name="contexto_id" value="{{ $contextoId }}">

                <div class="form-group">
                    <label for="tipo_documento_id" class="form-label">Tipo de documento *</label>
                    <x-custom-select
                        name="tipo_documento_id"
                        id="tipo_documento_id"
                        :options="$tiposDocumento->map(fn($t) => ['value' => $t->id, 'label' => $t->nombre])->toArray()"
                        placeholder="Seleccionar tipo..."
                    />
                </div>

                <div class="form-group">
                    <label for="archivo" class="form-label">Archivo *</label>
                    <input type="file" id="archivo" name="archivo" class="input" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalSubirDocumento()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalSubirDocumento() {
    document.getElementById('modalSubirDocumento').classList.remove('hidden');
}

function cerrarModalSubirDocumento() {
    document.getElementById('modalSubirDocumento').classList.add('hidden');
    const form = document.getElementById('formSubirDocumento');
    if (form) form.reset();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalSubirDocumento();
    }
});

(function() {
    const form = document.getElementById('formSubirDocumento');
    if (!form) return;
    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.textContent = 'Subiendo...';
    });
})();
</script>
@endpush
