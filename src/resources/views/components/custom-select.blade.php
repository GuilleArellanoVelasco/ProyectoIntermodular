@props([
    'name' => 'filter',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Seleccionar...',
    'redirect' => false,
    'size' => '', // 'sm', 'lg', 'full'
])

@php
    $selectedOption = collect($options)->firstWhere('value', $selected);
    $selectedLabel = $selectedOption['label'] ?? $placeholder;
@endphp

<div class="custom-select {{ $size ? 'select-' . $size : '' }}"
     data-name="{{ $name }}"
     @if($redirect) data-redirect="true" @endif
     tabindex="0">

    <button type="button" class="custom-select-trigger" tabindex="-1">
        <span class="custom-select-value">{{ $selectedLabel }}</span>
        <svg class="custom-select-arrow" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div class="custom-select-dropdown">
        @foreach($options as $option)
            <div class="custom-select-option {{ ($selected == $option['value']) ? 'active' : '' }}"
                 data-value="{{ $option['value'] }}"
                 data-label="{{ strtolower($option['label']) }}">
                {{ $option['label'] }}
            </div>
        @endforeach
    </div>

    <input type="hidden" name="{{ $name }}" id="{{ $id ?? $name }}" value="{{ $selected ?? '' }}">
</div>

@once
@push('scripts')
<script>
// ============================================
// CUSTOM SELECT COMPONENT - JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initCustomSelects();
});

function initCustomSelects() {
    const customSelects = document.querySelectorAll('.custom-select');

    customSelects.forEach(select => {
        const trigger = select.querySelector('.custom-select-trigger');
        const dropdown = select.querySelector('.custom-select-dropdown');
        const options = select.querySelectorAll('.custom-select-option');
        const valueDisplay = select.querySelector('.custom-select-value');
        const hiddenInput = select.querySelector('input[type="hidden"]');

        if (!trigger || !dropdown || !options.length) return;

        // Variable para busqueda por teclado
        let searchString = '';

        // Abrir/cerrar dropdown
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();

            const wasOpen = select.classList.contains('open');
            closeAllSelects();

            if (!wasOpen) {
                select.classList.add('open');
                select.focus();
            }
        });

        // Busqueda por teclado (sin interfaz visible)
        select.addEventListener('keydown', function(e) {
            if (!select.classList.contains('open')) {
                // Abrir con Enter o Space o flechas
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    select.classList.add('open');
                }
                return;
            }

            // Navegacion con flechas
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                navigateOptions(select, options, e.key === 'ArrowDown' ? 1 : -1);
                return;
            }

            // Seleccionar con Enter
            if (e.key === 'Enter') {
                e.preventDefault();
                const highlighted = select.querySelector('.custom-select-option.highlighted');
                if (highlighted) {
                    highlighted.click();
                }
                return;
            }

            // Cerrar con Escape
            if (e.key === 'Escape') {
                e.preventDefault();
                closeAllSelects();
                return;
            }

            // Tab cierra y pasa al siguiente
            if (e.key === 'Tab') {
                closeAllSelects();
                return;
            }

            // Borrar con Backspace
            if (e.key === 'Backspace') {
                e.preventDefault();
                searchString = searchString.slice(0, -1);
                filterOptions(options, searchString);
                return;
            }

            // Busqueda por caracteres
            if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                searchString += e.key.toLowerCase();
                filterOptions(options, searchString);
            }
        });

        // Seleccionar opción
        options.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const label = this.textContent.trim();

                // Actualizar valor mostrado
                valueDisplay.textContent = label;

                // Actualizar input hidden
                if (hiddenInput) {
                    hiddenInput.value = value;
                }

                // Actualizar clases active y mostrar todas las opciones
                options.forEach(opt => opt.classList.remove('active', 'highlighted', 'hidden'));
                this.classList.add('active');

                // Resetear busqueda
                searchString = '';

                // Cerrar dropdown
                select.classList.remove('open');

                // Disparar evento personalizado
                select.dispatchEvent(new CustomEvent('change', {
                    detail: { value, label }
                }));

                // Redirección automática si está configurado
                if (select.getAttribute('data-redirect') === 'true') {
                    handleAutoRedirect(select, value);
                }
            });

            // Highlight on hover
            option.addEventListener('mouseenter', function() {
                options.forEach(opt => opt.classList.remove('highlighted'));
                this.classList.add('highlighted');
            });
        });
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', closeAllSelects);
}

function filterOptions(options, query) {
    let firstVisible = null;
    options.forEach(opt => {
        const label = opt.getAttribute('data-label') || opt.textContent.toLowerCase();
        const matches = query === '' || label.includes(query);

        if (matches) {
            opt.classList.remove('hidden');
            if (!firstVisible) firstVisible = opt;
        } else {
            opt.classList.add('hidden');
        }
        opt.classList.remove('highlighted');
    });

    // Highlight first visible option
    if (firstVisible && query !== '') {
        firstVisible.classList.add('highlighted');
        firstVisible.scrollIntoView({ block: 'nearest' });
    }
}

function navigateOptions(select, options, direction) {
    // Solo considerar opciones visibles
    const visibleOptions = [...options].filter(opt => !opt.classList.contains('hidden'));
    if (visibleOptions.length === 0) return;

    let currentIndex = visibleOptions.findIndex(opt => opt.classList.contains('highlighted'));

    // Si no hay ninguna resaltada, empezar desde la activa o la primera
    if (currentIndex === -1) {
        currentIndex = visibleOptions.findIndex(opt => opt.classList.contains('active'));
        if (currentIndex === -1) currentIndex = direction === 1 ? -1 : visibleOptions.length;
    }

    const nextIndex = currentIndex + direction;

    if (nextIndex >= 0 && nextIndex < visibleOptions.length) {
        visibleOptions.forEach(opt => opt.classList.remove('highlighted'));
        visibleOptions[nextIndex].classList.add('highlighted');
        visibleOptions[nextIndex].scrollIntoView({ block: 'nearest' });
    }
}

function closeAllSelects() {
    document.querySelectorAll('.custom-select.open').forEach(select => {
        select.classList.remove('open');
        // Mostrar todas las opciones y quitar highlight
        select.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('highlighted', 'hidden');
        });
    });
}

function handleAutoRedirect(select, value) {
    const inputName = select.getAttribute('data-name') || 'filter';
    const currentUrl = new URL(window.location.href);

    if (value === 'todos' || value === 'all') {
        currentUrl.searchParams.delete(inputName);
    } else {
        currentUrl.searchParams.set(inputName, value);
    }

    window.location.href = currentUrl.toString();
}
</script>
@endpush
@endonce
