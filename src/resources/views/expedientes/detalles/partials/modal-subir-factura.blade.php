{{-- Modal para subir el PDF de una factura de honorarios --}}
<div id="modalSubirFactura" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalSubirFactura()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Subir factura</h3>
            <p class="text-sm text-text-secondary mb-4">
                Adjunta el PDF de la factura <span id="modalSubirFacturaNumero" class="font-mono text-primary-400"></span>.
            </p>

            <form id="formSubirFactura" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="archivo_factura" class="form-label">Archivo PDF *</label>
                    <input type="file" id="archivo_factura" name="archivo"
                           class="input" required accept="application/pdf">
                    <p class="text-xs text-text-muted mt-1">Solo PDF. Máximo 10 MB.</p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalSubirFactura()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalSubirFactura(facturaId, numeroFactura) {
    document.getElementById('formSubirFactura').action = '/facturas-honorarios/' + facturaId + '/subir-pdf';
    document.getElementById('modalSubirFacturaNumero').textContent = numeroFactura;
    document.getElementById('archivo_factura').value = '';
    document.getElementById('modalSubirFactura').classList.remove('hidden');
}

function cerrarModalSubirFactura() {
    document.getElementById('modalSubirFactura').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalSubirFactura();
    }
});

(function() {
    const form = document.getElementById('formSubirFactura');
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
