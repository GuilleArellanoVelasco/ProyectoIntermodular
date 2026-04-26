{{-- Modal para editar una línea del plan de pagos (deuda y propuesta) --}}
{{-- Requiere: $expediente --}}
<div id="modalEditarAcreedor" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalEditarAcreedor()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Editar Importes</h3>
            <p class="text-sm text-text-secondary mb-4">
                Acreedor: <strong id="editarAcreedorNombre" class="text-text-primary"></strong>
            </p>

            <form method="POST" id="formEditarAcreedor">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="editar_deuda_original" class="form-label">Deuda original (EUR) *</label>
                    <input type="number" id="editar_deuda_original" name="deuda_original"
                           class="input" min="0.01" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="editar_propuesta" class="form-label">Propuesta de pago (EUR) *</label>
                    <input type="number" id="editar_propuesta" name="propuesta"
                           class="input" min="0" step="0.01" required>
                    <span class="form-error hidden" id="editarPropuestaError">
                        La propuesta no puede superar la deuda original.
                    </span>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalEditarAcreedor()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalEditarAcreedor(id, nombre, deudaOriginal, propuesta) {
    const form = document.getElementById('formEditarAcreedor');
    form.action = `/expedientes/{{ $expediente->id }}/plan-pago-acreedores/${id}`;
    document.getElementById('editarAcreedorNombre').textContent = nombre;
    document.getElementById('editar_deuda_original').value = deudaOriginal;
    document.getElementById('editar_propuesta').value = propuesta;
    document.getElementById('editarPropuestaError').classList.add('hidden');
    document.getElementById('editar_propuesta').classList.remove('input-error');
    document.getElementById('modalEditarAcreedor').classList.remove('hidden');
}

function cerrarModalEditarAcreedor() {
    document.getElementById('modalEditarAcreedor').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalEditarAcreedor();
    }
});

(function() {
    const form = document.getElementById('formEditarAcreedor');
    if (!form) return;

    const deuda = document.getElementById('editar_deuda_original');
    const propuesta = document.getElementById('editar_propuesta');
    const error = document.getElementById('editarPropuestaError');

    function validar() {
        const d = parseFloat(deuda.value);
        const p = parseFloat(propuesta.value);
        if (!isNaN(d) && !isNaN(p) && p > d) {
            propuesta.classList.add('input-error');
            error.classList.remove('hidden');
            return false;
        }
        propuesta.classList.remove('input-error');
        error.classList.add('hidden');
        return true;
    }

    deuda.addEventListener('input', validar);
    propuesta.addEventListener('input', validar);

    form.addEventListener('submit', function(e) {
        if (!validar()) {
            e.preventDefault();
            return;
        }
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        }
    });
})();
</script>
@endpush
