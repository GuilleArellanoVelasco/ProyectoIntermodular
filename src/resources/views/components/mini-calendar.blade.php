@props([
    'month' => null,
    'year' => null,
    'events' => [],
    'selectedDate' => null,
])

@php
    $initialMonth = (int) ($month ?? date('n'));
    $initialYear = (int) ($year ?? date('Y'));
    $todayDay = (int) date('j');
    $todayMonth = (int) date('n');
    $todayYear = (int) date('Y');
    $calendarId = 'mini-cal-' . uniqid();
@endphp

<div id="{{ $calendarId }}"
     class="mini-calendar bg-bg-dark border border-white/5 rounded-xl p-5"
     data-today-day="{{ $todayDay }}"
     data-today-month="{{ $todayMonth }}"
     data-today-year="{{ $todayYear }}"
     data-initial-month="{{ $initialMonth }}"
     data-initial-year="{{ $initialYear }}"
     data-selected-date="{{ $selectedDate }}"
     data-events="{{ json_encode($events) }}">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
        <button type="button" class="mini-cal-prev w-8 h-8 flex items-center justify-center bg-white/2 border border-white/5 rounded-lg text-text-secondary cursor-pointer transition-all duration-200 hover:bg-primary-400/10 hover:border-primary-400 hover:text-primary-400" aria-label="Mes anterior">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="mini-cal-label text-lg font-bold font-display text-text-primary"></div>

        <button type="button" class="mini-cal-next w-8 h-8 flex items-center justify-center bg-white/2 border border-white/5 rounded-lg text-text-secondary cursor-pointer transition-all duration-200 hover:bg-primary-400/10 hover:border-primary-400 hover:text-primary-400" aria-label="Mes siguiente">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Weekdays --}}
    <div class="grid grid-cols-7 gap-1 mb-3">
        @foreach(['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $day)
            <div class="text-center text-xs font-semibold text-text-muted p-2">{{ $day }}</div>
        @endforeach
    </div>

    {{-- Days (renderizado por JS) --}}
    <div class="mini-cal-days grid grid-cols-7 gap-1 mb-4"></div>
</div>

@once
@push('scripts')
<script>
(function() {
    const MONTH_NAMES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function initMiniCalendar(cal) {
        const today = {
            day: parseInt(cal.dataset.todayDay, 10),
            month: parseInt(cal.dataset.todayMonth, 10),
            year: parseInt(cal.dataset.todayYear, 10),
        };
        const initialMonth = parseInt(cal.dataset.initialMonth, 10);
        const initialYear = parseInt(cal.dataset.initialYear, 10);
        const events = JSON.parse(cal.dataset.events || '[]');

        // Fecha seleccionada (YYYY-MM-DD). Si está presente, habilita
        // navegación al hacer click en un día y marca ese día como seleccionado.
        const selectedDateStr = cal.dataset.selectedDate || '';
        let selected = null;
        if (selectedDateStr) {
            const parts = selectedDateStr.split('-');
            selected = {
                year: parseInt(parts[0], 10),
                month: parseInt(parts[1], 10),
                day: parseInt(parts[2], 10),
            };
        }

        let month = initialMonth;
        let year = initialYear;

        const label = cal.querySelector('.mini-cal-label');
        const daysContainer = cal.querySelector('.mini-cal-days');
        const prevBtn = cal.querySelector('.mini-cal-prev');
        const nextBtn = cal.querySelector('.mini-cal-next');

        function pad(n) { return String(n).padStart(2, '0'); }

        function render() {
            label.textContent = MONTH_NAMES[month - 1] + ' ' + year;

            const firstDay = new Date(year, month - 1, 1);
            // Lunes=1..Domingo=7 (ISO)
            let firstWeekday = firstDay.getDay();
            firstWeekday = firstWeekday === 0 ? 7 : firstWeekday;

            const daysInMonth = new Date(year, month, 0).getDate();

            // Agrupar eventos por día, solo si pertenecen al mes/año inicial (los eventos
            // vienen precargados para ese mes; navegar a otro mes no tiene datos).
            const eventsByDay = {};
            const viewingInitialMonth = (month === initialMonth && year === initialYear);
            if (viewingInitialMonth) {
                events.forEach(ev => {
                    const d = ev.day;
                    if (!eventsByDay[d]) eventsByDay[d] = [];
                    eventsByDay[d].push(ev);
                });
            }

            let html = '';
            for (let i = 1; i < firstWeekday; i++) {
                html += '<div class="aspect-square"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = (day === today.day && month === today.month && year === today.year);
                const isSelected = selected && (day === selected.day && month === selected.month && year === selected.year);
                const dayEvents = eventsByDay[day] || [];
                const hasEvents = dayEvents.length > 0;
                const hasHigh = dayEvents.some(e => e.priority === 'alta');

                let classes = 'aspect-square flex flex-col items-center justify-center gap-1 p-1 rounded-lg text-sm cursor-pointer transition-all duration-200 relative ';
                if (isSelected) {
                    classes += 'bg-primary-400 text-white font-bold';
                } else if (isToday) {
                    classes += 'bg-primary-400/20 text-primary-400 font-bold border border-primary-400';
                } else {
                    classes += 'text-text-secondary hover:bg-white/5 hover:text-text-primary';
                }
                if (hasHigh && !isSelected) classes += ' border border-error/30';

                const title = hasEvents ? (dayEvents.length + ' evento(s)') : '';
                const dateIso = year + '-' + pad(month) + '-' + pad(day);

                html += '<div class="' + classes + '" title="' + title + '" data-date="' + dateIso + '">';
                html += '<span class="text-sm">' + day + '</span>';

                if (hasEvents) {
                    html += '<div class="flex gap-0.5 mt-auto">';
                    const maxDots = Math.min(dayEvents.length, 3);
                    for (let i = 0; i < maxDots; i++) {
                        const priority = dayEvents[i].priority || 'media';
                        let dotColor;
                        switch (priority) {
                            case 'alta': dotColor = 'bg-error'; break;
                            case 'media': dotColor = 'bg-warning'; break;
                            case 'baja': dotColor = 'bg-accent-blue'; break;
                            default: dotColor = 'bg-primary-400';
                        }
                        html += '<span class="w-1 h-1 rounded-full ' + dotColor + '"></span>';
                    }
                    html += '</div>';
                }

                html += '</div>';
            }

            daysContainer.innerHTML = html;
        }

        prevBtn.addEventListener('click', function() {
            month--;
            if (month < 1) { month = 12; year--; }
            render();
        });

        nextBtn.addEventListener('click', function() {
            month++;
            if (month > 12) { month = 1; year++; }
            render();
        });

        // Click en un día: navegar a /dashboard?fecha=YYYY-MM-DD
        daysContainer.addEventListener('click', function(e) {
            const cell = e.target.closest('[data-date]');
            if (!cell) return;
            const fecha = cell.dataset.date;
            if (!fecha) return;
            const url = new URL(window.location.href);
            url.searchParams.set('fecha', fecha);
            window.location.href = url.pathname + '?' + url.searchParams.toString();
        });

        render();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mini-calendar').forEach(initMiniCalendar);
    });
})();
</script>
@endpush
@endonce
