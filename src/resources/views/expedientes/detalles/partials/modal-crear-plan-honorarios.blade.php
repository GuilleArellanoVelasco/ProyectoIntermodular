{{-- Modal para crear plan de pago de honorarios --}}
{{-- Requiere: $expediente --}}
<div id="modalCrearPlanHonorarios" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalCrearPlanHonorarios()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Crear Plan de Pagos</h3>
            <p class="text-sm text-text-secondary mb-4">
                Introduce el importe total y el número de cuotas. Las cuotas se calcularán de forma mensual a partir del primer vencimiento.
            </p>

            <form action="{{ route('plan-pago-honorarios.store', $expediente) }}"
                  method="POST" id="formCrearPlanHonorarios">
                @csrf

                <div class="form-group">
                    <label for="importe_total" class="form-label">Importe total (EUR) *</label>
                    <input type="number" id="importe_total" name="importe_total"
                           class="input" min="0.01" step="0.01" required
                           value="{{ old('importe_total') }}">
                </div>

                <div class="form-group">
                    <label for="numero_cuotas" class="form-label">Número de cuotas *</label>
                    <input type="number" id="numero_cuotas" name="numero_cuotas"
                           class="input" min="1" max="120" step="1" required
                           value="{{ old('numero_cuotas') }}">
                </div>

                <div class="form-group">
                    <label for="fecha_primer_vencimiento" class="form-label">Fecha del primer vencimiento *</label>
                    <input type="date" id="fecha_primer_vencimiento" name="fecha_primer_vencimiento"
                           class="input" required
                           value="{{ old('fecha_primer_vencimiento') }}">
                </div>

                <div class="p-3 bg-bg-medium rounded-lg mb-4">
                    <div class="text-xs text-text-muted mb-1">Importe mensual calculado</div>
                    <div class="text-xl font-bold text-primary-400" id="planHonorariosCuotaPreview">— EUR</div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalCrearPlanHonorarios()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalCrearPlanHonorarios() {
    document.getElementById('modalCrearPlanHonorarios').classList.remove('hidden');
}

function cerrarModalCrearPlanHonorarios() {
    document.getElementById('modalCrearPlanHonorarios').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalCrearPlanHonorarios();
    }
});

(function() {
    const form = document.getElementById('formCrearPlanHonorarios');
    if (!form) return;

    const totalInput = document.getElementById('importe_total');
    const nInput = document.getElementById('numero_cuotas');
    const preview = document.getElementById('planHonorariosCuotaPreview');

    function updatePreview() {
        const total = parseFloat(totalInput.value);
        const n = parseInt(nInput.value, 10);
        if (!isNaN(total) && total > 0 && !isNaN(n) && n > 0) {
            const cuota = Math.floor((total / n) * 100) / 100;
            preview.textContent = cuota.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR';
        } else {
            preview.textContent = '— EUR';
        }
    }

    totalInput.addEventListener('input', updatePreview);
    nInput.addEventListener('input', updatePreview);
    updatePreview();

    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.textContent = 'Creando...';
    });
})();
</script>
@endpush
