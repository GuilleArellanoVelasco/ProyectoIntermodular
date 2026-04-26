{{-- Formulario compartido de cliente (crear + editar) --}}
{{-- Requiere: $cliente (nullable), $tiposDocumentacion, $action, $method ('POST'|'PUT') --}}
@php
    $isEdit = !is_null($cliente ?? null);
    $tiposDocOptions = $tiposDocumentacion->map(fn($tipo) => [
        'value' => (string) $tipo->id,
        'label' => $tipo->tipo_documento,
    ])->toArray();

    $checkEmpresa = old('es_empresa', $isEdit && $cliente->empresa_id ? 1 : 0);
    $checkConsorte = old('tiene_consorte', $isEdit && $cliente->consorte ? 1 : 0);
@endphp

<div class="max-w-4xl mx-auto animate-fade-in">
    <form action="{{ $action }}" method="POST" class="card" id="clienteForm" novalidate>
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        {{-- Datos del Cliente --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Datos Personales</h3>

            {{-- Nombre y Apellidos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="input" value="{{ old('nombre', $cliente?->nombre) }}">
                    <span class="form-error hidden" id="nombreError"></span>
                </div>

                <div class="form-group">
                    <label for="apellido1" class="form-label">Primer Apellido *</label>
                    <input type="text" id="apellido1" name="apellido1" class="input" value="{{ old('apellido1', $cliente?->apellido1) }}">
                    <span class="form-error hidden" id="apellido1Error"></span>
                </div>

                <div class="form-group">
                    <label for="apellido2" class="form-label">Segundo Apellido</label>
                    <input type="text" id="apellido2" name="apellido2" class="input" value="{{ old('apellido2', $cliente?->apellido2) }}">
                </div>
            </div>

            {{-- Documentacion --}}
            <div class="grid grid-cols-1 md:grid-cols-[1fr_2fr] gap-5 mb-5">
                <div class="form-group">
                    <label for="tipo_documentacion" class="form-label">Tipo de Documento *</label>
                    <x-custom-select
                        name="tipo_documentacion_id"
                        id="tipo_documentacion_id"
                        :options="$tiposDocOptions"
                        placeholder="Seleccionar..."
                        :selected="old('tipo_documentacion_id', $cliente?->tipo_documentacion_id)"
                    />
                    <span class="form-error hidden" id="tipo_documentacion_idError"></span>
                </div>

                <div class="form-group">
                    <label for="numero_documentacion" class="form-label">Numero de Documento *</label>
                    <input type="text" id="numero_documentacion" name="numero_documentacion" class="input" value="{{ old('numero_documentacion', $cliente?->numero_documentacion) }}">
                    <span class="form-error hidden" id="numero_documentacionError"></span>
                </div>
            </div>

            {{-- Contacto --}}
            <div class="mb-4">
                <h4 class="text-base font-semibold text-text-secondary mb-3">Contacto</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="input" value="{{ old('email', $cliente?->email) }}">
                        <span class="form-error hidden" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="form-label">Telefono</label>
                        <input type="tel" id="telefono" name="telefono" class="input" value="{{ old('telefono', $cliente?->telefono) }}">
                    </div>
                </div>
            </div>

            {{-- Direccion --}}
            <div class="form-group">
                <label for="direccion" class="form-label">Direccion</label>
                <textarea id="direccion" name="direccion" rows="3" class="input">{{ old('direccion', $cliente?->direccion) }}</textarea>
            </div>

            {{-- Checkbox Es Empresa --}}
            <div class="mt-5">
                <label class="checkbox-label">
                    <input type="checkbox" id="es_empresa" name="es_empresa" value="1" {{ $checkEmpresa ? 'checked' : '' }}>
                    <span>¿Es empresa?</span>
                </label>
            </div>

            {{-- Datos de Empresa --}}
            <div id="datosEmpresa" class="{{ $checkEmpresa ? '' : 'hidden' }} mt-5 p-6 bg-white/2 border border-white/5 rounded-xl transition-all duration-200">
                <h4 class="text-lg font-bold text-text-primary mb-5">Datos de la Empresa</h4>

                <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-5 mb-5">
                    <div class="form-group">
                        <label for="empresa_nombre" class="form-label">Nombre de la Empresa *</label>
                        <input type="text" id="empresa_nombre" name="empresa_nombre" class="input" value="{{ old('empresa_nombre', $cliente?->empresa?->nombre) }}">
                        <span class="form-error hidden" id="empresa_nombreError"></span>
                    </div>

                    <div class="form-group">
                        <label for="empresa_cif" class="form-label">CIF *</label>
                        <input type="text" id="empresa_cif" name="empresa_cif" class="input" value="{{ old('empresa_cif', $cliente?->empresa?->cif) }}">
                        <span class="form-error hidden" id="empresa_cifError"></span>
                    </div>
                </div>

                {{-- Contacto Empresa --}}
                <div class="mb-4">
                    <h4 class="text-base font-semibold text-text-secondary mb-3">Contacto</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="form-group">
                            <label for="empresa_email" class="form-label">Email</label>
                            <input type="email" id="empresa_email" name="empresa_email" class="input" value="{{ old('empresa_email', $cliente?->empresa?->email) }}">
                            <span class="form-error hidden" id="empresa_emailError"></span>
                        </div>

                        <div class="form-group">
                            <label for="empresa_telefono" class="form-label">Telefono</label>
                            <input type="tel" id="empresa_telefono" name="empresa_telefono" class="input" value="{{ old('empresa_telefono', $cliente?->empresa?->telefono) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="empresa_direccion" class="form-label">Direccion</label>
                    <textarea id="empresa_direccion" name="empresa_direccion" rows="3" class="input">{{ old('empresa_direccion', $cliente?->empresa?->direccion) }}</textarea>
                </div>
            </div>

            {{-- Checkbox Tiene Consorte --}}
            <div class="mt-5">
                <label class="checkbox-label">
                    <input type="checkbox" id="tiene_consorte" name="tiene_consorte" value="1" {{ $checkConsorte ? 'checked' : '' }}>
                    <span>¿Tiene consorte?</span>
                </label>
            </div>

            {{-- Datos del Consorte --}}
            <div id="datosConsorte" class="{{ $checkConsorte ? '' : 'hidden' }} mt-5 p-6 bg-white/2 border border-white/5 rounded-xl transition-all duration-200">
                <h4 class="text-lg font-bold text-text-primary mb-5">Datos del Consorte</h4>

                {{-- Nombre y Apellidos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div class="form-group">
                        <label for="consorte_nombre" class="form-label">Nombre *</label>
                        <input type="text" id="consorte_nombre" name="consorte_nombre" class="input" value="{{ old('consorte_nombre', $cliente?->consorte?->nombre) }}">
                        <span class="form-error hidden" id="consorte_nombreError"></span>
                    </div>

                    <div class="form-group">
                        <label for="consorte_apellido1" class="form-label">Primer Apellido *</label>
                        <input type="text" id="consorte_apellido1" name="consorte_apellido1" class="input" value="{{ old('consorte_apellido1', $cliente?->consorte?->apellido1) }}">
                        <span class="form-error hidden" id="consorte_apellido1Error"></span>
                    </div>

                    <div class="form-group">
                        <label for="consorte_apellido2" class="form-label">Segundo Apellido</label>
                        <input type="text" id="consorte_apellido2" name="consorte_apellido2" class="input" value="{{ old('consorte_apellido2', $cliente?->consorte?->apellido2) }}">
                    </div>
                </div>

                {{-- Documentacion --}}
                <div class="grid grid-cols-1 md:grid-cols-[1fr_2fr] gap-5 mb-5">
                    <div class="form-group">
                        <label for="consorte_tipo_documentacion" class="form-label">Tipo de Documento *</label>
                        <x-custom-select
                            name="consorte_tipo_documentacion_id"
                            id="consorte_tipo_documentacion_id"
                            :options="$tiposDocOptions"
                            placeholder="Seleccionar..."
                            :selected="old('consorte_tipo_documentacion_id', $cliente?->consorte?->tipo_documentacion_id)"
                        />
                        <span class="form-error hidden" id="consorte_tipo_documentacion_idError"></span>
                    </div>

                    <div class="form-group">
                        <label for="consorte_numero_documentacion" class="form-label">Numero de Documento *</label>
                        <input type="text" id="consorte_numero_documentacion" name="consorte_numero_documentacion" class="input" value="{{ old('consorte_numero_documentacion', $cliente?->consorte?->numero_documentacion) }}">
                        <span class="form-error hidden" id="consorte_numero_documentacionError"></span>
                    </div>
                </div>

                {{-- Contacto --}}
                <div>
                    <h4 class="text-base font-semibold text-text-secondary mb-3">Contacto</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="form-group">
                            <label for="consorte_email" class="form-label">Email</label>
                            <input type="email" id="consorte_email" name="consorte_email" class="input" value="{{ old('consorte_email', $cliente?->consorte?->email) }}">
                            <span class="form-error hidden" id="consorte_emailError"></span>
                        </div>

                        <div class="form-group">
                            <label for="consorte_telefono" class="form-label">Telefono</label>
                            <input type="tel" id="consorte_telefono" name="consorte_telefono" class="input" value="{{ old('consorte_telefono', $cliente?->consorte?->telefono) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-4 pt-6 border-t border-white/5">
            <a href="{{ $isEdit ? route('clientes.show', $cliente) : route('clientes') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $isEdit ? 'Actualizar Cliente' : 'Guardar Cliente' }}
            </button>
        </div>
    </form>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('clienteForm');
    if (!form) return;
    const esEmpresaCheckbox = document.getElementById('es_empresa');
    const tieneConsorteCheckbox = document.getElementById('tiene_consorte');
    const datosEmpresa = document.getElementById('datosEmpresa');
    const datosConsorte = document.getElementById('datosConsorte');

    esEmpresaCheckbox.addEventListener('change', function() {
        if (this.checked) {
            datosEmpresa.classList.remove('hidden');
        } else {
            datosEmpresa.classList.add('hidden');
            clearSectionErrors('empresa');
        }
    });

    tieneConsorteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            datosConsorte.classList.remove('hidden');
        } else {
            datosConsorte.classList.add('hidden');
            clearSectionErrors('consorte');
        }
    });

    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearError(this);
        });
    });

    form.querySelectorAll('.custom-select').forEach(select => {
        const hiddenInput = select.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            const observer = new MutationObserver(() => {
                clearError(hiddenInput);
            });
            observer.observe(hiddenInput, { attributes: true, attributeFilter: ['value'] });
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        isValid = validateRequired('nombre', 'El nombre es obligatorio') && isValid;
        isValid = validateRequired('apellido1', 'El primer apellido es obligatorio') && isValid;
        isValid = validateRequired('tipo_documentacion_id', 'Selecciona un tipo de documento') && isValid;
        isValid = validateRequired('numero_documentacion', 'El numero de documento es obligatorio') && isValid;

        const email = document.getElementById('email');
        if (email.value.trim() !== '' && !isValidEmail(email.value)) {
            showError(email, 'Ingresa un correo valido (ejemplo@dominio.com)');
            isValid = false;
        }

        if (esEmpresaCheckbox.checked) {
            isValid = validateRequired('empresa_nombre', 'El nombre de la empresa es obligatorio') && isValid;
            isValid = validateRequired('empresa_cif', 'El CIF es obligatorio') && isValid;

            const empresaEmail = document.getElementById('empresa_email');
            if (empresaEmail.value.trim() !== '' && !isValidEmail(empresaEmail.value)) {
                showError(empresaEmail, 'Ingresa un correo valido');
                isValid = false;
            }
        }

        if (tieneConsorteCheckbox.checked) {
            isValid = validateRequired('consorte_nombre', 'El nombre del consorte es obligatorio') && isValid;
            isValid = validateRequired('consorte_apellido1', 'El primer apellido del consorte es obligatorio') && isValid;
            isValid = validateRequired('consorte_tipo_documentacion_id', 'Selecciona un tipo de documento') && isValid;
            isValid = validateRequired('consorte_numero_documentacion', 'El numero de documento es obligatorio') && isValid;

            const consorteEmail = document.getElementById('consorte_email');
            if (consorteEmail.value.trim() !== '' && !isValidEmail(consorteEmail.value)) {
                showError(consorteEmail, 'Ingresa un correo valido');
                isValid = false;
            }
        }

        if (isValid) {
            form.submit();
        } else {
            const firstError = form.querySelector('.form-error:not(.hidden)');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    function validateRequired(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return true;

        const value = field.value.trim();
        if (value === '') {
            showError(field, message);
            return false;
        }
        return true;
    }

    function showError(input, message) {
        const customSelect = input.closest('.custom-select');
        if (customSelect) {
            customSelect.querySelector('.custom-select-trigger').classList.add('input-error');
        } else {
            input.classList.add('input-error');
        }

        const errorSpan = document.getElementById(input.id + 'Error');
        if (errorSpan) {
            errorSpan.textContent = message;
            errorSpan.classList.remove('hidden');
        }
    }

    function clearError(input) {
        const customSelect = input.closest('.custom-select');
        if (customSelect) {
            customSelect.querySelector('.custom-select-trigger').classList.remove('input-error');
        } else {
            input.classList.remove('input-error');
        }

        const errorSpan = document.getElementById(input.id + 'Error');
        if (errorSpan) {
            errorSpan.classList.add('hidden');
            errorSpan.textContent = '';
        }
    }

    function clearSectionErrors(section) {
        const prefix = section + '_';
        form.querySelectorAll(`[id^="${prefix}"]`).forEach(el => {
            if (el.classList.contains('input')) {
                clearError(el);
            }
        });
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});
</script>
@endpush
@endonce
