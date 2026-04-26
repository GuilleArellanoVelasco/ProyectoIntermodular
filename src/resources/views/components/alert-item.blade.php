@props([
    'tipo' => 'alerta',
    'day',
    'month',
    'title',
    'description' => null,
    'reference' => null,
    'eventoId' => null,
    'canResolve' => true,
])

@php
    $isAlerta = $tipo === 'alerta';
    $borderClass = $isAlerta ? 'border-warning' : 'border-accent-blue';
    $dateColor = $isAlerta ? 'text-warning' : 'text-accent-blue';
@endphp

<div class="flex gap-4 p-4 bg-white/2 rounded-xl border-l-4 {{ $borderClass }} hover:bg-white/4 transition-all duration-200">
    <div class="flex flex-col items-center justify-center w-[60px] shrink-0">
        <div class="text-2xl font-bold {{ $dateColor }} leading-none">{{ $day }}</div>
        <div class="text-xs text-text-secondary uppercase">{{ $month }}</div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            <span class="font-semibold text-text-primary truncate">{{ $title }}</span>
        </div>

        @if($description)
            <div class="text-sm text-text-secondary mb-1">{{ $description }}</div>
        @endif

        @if($reference)
            <div class="text-sm text-text-muted">{{ $reference }}</div>
        @endif
    </div>

    @if($canResolve && $eventoId && !$isAlerta)
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('eventos.resolver', $eventoId) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="action-icon" title="Marcar como resuelta">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </form>
        </div>
    @endif
</div>
