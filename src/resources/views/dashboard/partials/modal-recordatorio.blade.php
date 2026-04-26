{{-- Modal para crear un recordatorio personal --}}
<div id="modalRecordatorio" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="cerrarModalRecordatorio()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-bg-dark border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-text-primary mb-2">Nuevo Recordatorio</h3>
            <p class="text-sm text-text-secondary mb-4">
                Crea un recordatorio personal que aparecerá en tu calendario.
            </p>

            <form action="{{ route('recordatorios.store') }}" method="POST" id="formRecordatorio">
                @csrf

                <div class="form-group">
                    <label for="recordatorio_titulo" class="form-label">Título *</label>
                    <input type="text" id="recordatorio_titulo" name="titulo" class="input"
                           maxlength="255" required value="{{ old('titulo') }}">
                    @error('titulo')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="recordatorio_fecha" class="form-label">Fecha *</label>
                    <input type="date" id="recordatorio_fecha" name="fecha" class="input"
                           min="{{ now()->toDateString() }}" required value="{{ old('fecha') }}">
                    @error('fecha')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="recordatorio_descripcion" class="form-label">Descripción</label>
                    <textarea id="recordatorio_descripcion" name="descripcion" class="input"
                              rows="3">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalRecordatorio()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalRecordatorio() {
    document.getElementById('modalRecordatorio').classList.remove('hidden');
}

function cerrarModalRecordatorio() {
    document.getElementById('modalRecordatorio').classList.add('hidden');
    const form = document.getElementById('formRecordatorio');
    if (form) form.reset();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalRecordatorio();
    }
});

@if($errors->any() && old('titulo'))
    abrirModalRecordatorio();
@endif
</script>
@endpush
