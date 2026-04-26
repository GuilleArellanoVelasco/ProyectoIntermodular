{{-- Modal para registrar pago de cuota de honorarios --}}
{{-- Requiere: $expediente, $plan, $siguienteCuota --}}
<div id="modalRegistrarPagoHonorarios" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalRegistrarPagoHonorarios()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Registrar Pago</h3>
            <p class="text-sm text-text-secondary mb-4">
                Se registrará el pago de la siguiente cuota pendiente y se generará la factura asociada.
            </p>

            <div class="p-4 bg-bg-medium rounded-lg mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-text-muted">Cuota</span>
                    <span class="font-semibold text-text-primary">
                        {{ $siguienteCuota->numero_cuota }}/{{ $plan->numero_cuotas }}
                    </span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-text-muted">Vencimiento</span>
                    <span class="text-sm text-text-primary">
                        {{ $siguienteCuota->fecha_vencimiento?->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-text-muted">Importe</span>
                    <span class="text-xl font-bold text-primary-400">
                        {{ number_format($siguienteCuota->importe, 2, ',', '.') }} EUR
                    </span>
                </div>
            </div>

            <form action="{{ route('plan-pago-honorarios.registrar-pago', $expediente) }}"
                  method="POST" id="formRegistrarPagoHonorarios">
                @csrf

                <div class="form-group">
                    <label for="fecha_pago" class="form-label">Fecha de pago *</label>
                    <input type="date" id="fecha_pago" name="fecha_pago"
                           class="input" required
                           value="{{ old('fecha_pago', now()->toDateString()) }}">
                </div>

                <div class="form-group">
                    <label for="metodo_pago" class="form-label">Método de pago *</label>
                    <x-custom-select
                        name="metodo_pago"
                        id="metodo_pago"
                        :options="[
                            ['value' => 'transferencia', 'label' => 'Transferencia'],
                            ['value' => 'efectivo', 'label' => 'Efectivo'],
                            ['value' => 'tarjeta', 'label' => 'Tarjeta'],
                            ['value' => 'domiciliacion', 'label' => 'Domiciliación'],
                        ]"
                        :selected="old('metodo_pago', 'transferencia')"
                        placeholder="Seleccionar método..."
                    />
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalRegistrarPagoHonorarios()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalRegistrarPagoHonorarios() {
    document.getElementById('modalRegistrarPagoHonorarios').classList.remove('hidden');
}

function cerrarModalRegistrarPagoHonorarios() {
    document.getElementById('modalRegistrarPagoHonorarios').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalRegistrarPagoHonorarios();
    }
});

(function() {
    const form = document.getElementById('formRegistrarPagoHonorarios');
    if (!form) return;
    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.textContent = 'Registrando...';
    });
})();
</script>
@endpush
