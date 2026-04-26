{{-- Modal para añadir un acreedor al plan de pagos --}}
{{-- Requiere: $expediente --}}
<div id="modalAnadirAcreedor" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalAnadirAcreedor()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Añadir Acreedor al Plan</h3>
            <p class="text-sm text-text-secondary mb-4">
                Registra un nuevo acreedor indicando su nombre, la deuda original y la cantidad propuesta a pagar.
            </p>

            <form action="{{ route('plan-pago-acreedores.store', $expediente) }}" method="POST" id="formAnadirAcreedor">
                @csrf

                <div class="form-group">
                    <label for="nombre_acreedor" class="form-label">Nombre del acreedor *</label>
                    <input type="text" id="nombre_acreedor" name="nombre_acreedor"
                           class="input" maxlength="255" required
                           value="{{ old('nombre_acreedor') }}"
                           placeholder="Ej. BBVA, Endesa, Hacienda...">
                </div>

                <div class="form-group">
                    <label for="anadir_deuda_original" class="form-label">Deuda original (EUR) *</label>
                    <input type="number" id="anadir_deuda_original" name="deuda_original"
                           class="input" min="0.01" step="0.01" required
                           value="{{ old('deuda_original') }}">
                </div>

                <div class="form-group">
                    <label for="anadir_propuesta" class="form-label">Propuesta de pago (EUR) *</label>
                    <input type="number" id="anadir_propuesta" name="propuesta"
                           class="input" min="0" step="0.01" required
                           value="{{ old('propuesta') }}">
                    <span class="form-error hidden" id="anadirPropuestaError">
                        La propuesta no puede superar la deuda original.
                    </span>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalAnadirAcreedor()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Añadir</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalAnadirAcreedor() {
    document.getElementById('modalAnadirAcreedor').classList.remove('hidden');
}

function cerrarModalAnadirAcreedor() {
    document.getElementById('modalAnadirAcreedor').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalAnadirAcreedor();
    }
});

(function() {
    const form = document.getElementById('formAnadirAcreedor');
    if (!form) return;

    const deuda = document.getElementById('anadir_deuda_original');
    const propuesta = document.getElementById('anadir_propuesta');
    const error = document.getElementById('anadirPropuestaError');

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
