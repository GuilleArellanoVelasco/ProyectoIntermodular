{{-- Modal para registrar el pago de una línea del plan --}}
{{-- Requiere: $expediente --}}
<div id="modalRegistrarPagoAcreedor" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalRegistrarPagoAcreedor()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Registrar Pago</h3>
            <p class="text-sm text-text-secondary mb-4">
                Al registrar el pago la línea quedará marcada como pagada y ya no se podrá modificar.
            </p>

            <div class="p-3 bg-bg-medium rounded-lg mb-4">
                <div class="text-xs text-text-muted mb-1">Acreedor</div>
                <div class="text-sm font-semibold text-text-primary mb-2" id="registrarPagoNombre">—</div>
                <div class="text-xs text-text-muted mb-1">Importe</div>
                <div class="text-xl font-bold text-primary-400" id="registrarPagoImporte">— EUR</div>
            </div>

            <form method="POST" id="formRegistrarPagoAcreedor">
                @csrf

                <div class="form-group">
                    <label for="registrar_fecha_pago" class="form-label">Fecha de pago *</label>
                    <input type="date" id="registrar_fecha_pago" name="fecha_pago"
                           class="input" required
                           value="{{ old('fecha_pago', date('Y-m-d')) }}">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalRegistrarPagoAcreedor()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalRegistrarPagoAcreedor(id, nombre, importe) {
    const form = document.getElementById('formRegistrarPagoAcreedor');
    form.action = `/expedientes/{{ $expediente->id }}/plan-pago-acreedores/${id}/registrar-pago`;
    document.getElementById('registrarPagoNombre').textContent = nombre;
    document.getElementById('registrarPagoImporte').textContent =
        Number(importe).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR';
    document.getElementById('modalRegistrarPagoAcreedor').classList.remove('hidden');
}

function cerrarModalRegistrarPagoAcreedor() {
    document.getElementById('modalRegistrarPagoAcreedor').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalRegistrarPagoAcreedor();
    }
});

(function() {
    const form = document.getElementById('formRegistrarPagoAcreedor');
    if (!form) return;

    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Registrando...';
        }
    });
})();
</script>
@endpush
