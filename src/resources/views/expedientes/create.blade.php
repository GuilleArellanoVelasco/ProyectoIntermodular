@extends('layouts.app')

@section('title', 'Nuevo Expediente')
@section('page-title', 'Crear Expediente')
@section('page-subtitle', 'Registrar un nuevo expediente en el sistema')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <form action="{{ route('expedientes.store') }}" method="POST" class="card" id="expedienteForm" novalidate>
        @csrf

        {{-- Datos Basicos del Expediente --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Datos Basicos</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="form-group">
                    <label for="cliente_id" class="form-label">Cliente *</label>
                    <x-custom-select
                        name="cliente_id"
                        id="cliente_id"
                        :options="$clientes->map(fn($c) => ['value' => $c->id, 'label' => $c->nombreCompleto])->toArray()"
                        placeholder="Seleccionar cliente..."
                        :selected="old('cliente_id', $clienteIdPreseleccionado ?? null)"
                    />
                    <span class="form-error hidden" id="cliente_idError"></span>
                </div>

                <div class="form-group">
                    <label for="gestor_id" class="form-label">Gestor Asignado *</label>
                    <x-custom-select
                        name="gestor_id"
                        id="gestor_id"
                        :options="$gestores->map(fn($g) => ['value' => $g->id, 'label' => $g->nombreCompleto])->toArray()"
                        placeholder="Seleccionar gestor..."
                        :selected="old('gestor_id', auth()->id())"
                    />
                    <span class="form-error hidden" id="gestor_idError"></span>
                </div>
            </div>

        </div>

        {{-- Tipo de Procedimiento --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-text-primary mb-6 pb-3 border-b border-white/5">Tipo de Procedimiento</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="tipo_procedimiento_id" class="form-label">Tipo *</label>
                    <x-custom-select
                        name="tipo_procedimiento_id"
                        id="tipo_procedimiento_id"
                        :options="$tiposProcedimiento->map(fn($t) => ['value' => $t->id, 'label' => $t->nombre])->toArray()"
                        placeholder="Seleccionar tipo..."
                        :selected="old('tipo_procedimiento_id')"
                    />
                    <span class="form-error hidden" id="tipo_procedimiento_idError"></span>
                </div>

                {{-- Campo condicional para "Otros" --}}
                <div class="form-group" id="tipo_encargo_container" style="display: none;">
                    <label for="tipo_encargo" class="form-label">Tipo de Encargo *</label>
                    <input type="text" id="tipo_encargo" name="tipo_encargo" class="input"
                           placeholder="Ej: Asesoria legal, Mediacion..."
                           value="{{ old('tipo_encargo') }}">
                    <span class="form-error hidden" id="tipo_encargoError"></span>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-4 pt-6 border-t border-white/5">
            <a href="{{ route('expedientes') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Crear Expediente
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('expedienteForm');
    const tipoEncargoContainer = document.getElementById('tipo_encargo_container');
    const tipoEncargoInput = document.getElementById('tipo_encargo');

    // ID del tipo "Otros"
    const TIPO_OTROS_ID = '4';

    // Toggle tipo_encargo cuando se selecciona "Otros"
    function toggleTipoEncargo() {
        const tipoProcedimientoInput = document.getElementById('tipo_procedimiento_id');
        const selectedValue = tipoProcedimientoInput?.value || '';

        if (selectedValue === TIPO_OTROS_ID) {
            tipoEncargoContainer.style.display = 'block';
        } else {
            tipoEncargoContainer.style.display = 'none';
            tipoEncargoInput.value = '';
            clearError(tipoEncargoInput);
        }
    }

    // Observar cambios en el custom-select de tipo_procedimiento
    const tipoProcedimientoSelect = document.querySelector('#tipo_procedimiento_id');
    if (tipoProcedimientoSelect) {
        const observer = new MutationObserver(toggleTipoEncargo);
        observer.observe(tipoProcedimientoSelect, { attributes: true, attributeFilter: ['value'] });
    }

    // Escuchar clicks en las opciones del custom-select
    document.addEventListener('click', function(e) {
        if (e.target.closest('.custom-select-option')) {
            setTimeout(toggleTipoEncargo, 50);
        }
    });

    // Estado inicial
    toggleTipoEncargo();

    // Limpiar errores al escribir
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearError(this);
        });
    });

    // Escuchar cambios en custom-select
    form.querySelectorAll('.custom-select').forEach(select => {
        const hiddenInput = select.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            const observer = new MutationObserver(() => {
                clearError(hiddenInput);
            });
            observer.observe(hiddenInput, { attributes: true, attributeFilter: ['value'] });
        }
    });

    // Validacion al enviar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Validar campos obligatorios
        isValid = validateRequired('cliente_id', 'El cliente es obligatorio') && isValid;
        isValid = validateRequired('gestor_id', 'El gestor es obligatorio') && isValid;
        isValid = validateRequired('tipo_procedimiento_id', 'El tipo de procedimiento es obligatorio') && isValid;

        // Validar tipo_encargo si tipo es "Otros"
        const tipoProcedimientoInput = document.getElementById('tipo_procedimiento_id');
        if (tipoProcedimientoInput?.value === TIPO_OTROS_ID) {
            isValid = validateRequired('tipo_encargo', 'El tipo de encargo es obligatorio para procedimientos de tipo "Otros"') && isValid;
        }

        if (isValid) {
            form.submit();
        } else {
            // Scroll al primer error visible
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
        // Si es un hidden input dentro de custom-select
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
});
</script>
@endpush
@endsection
