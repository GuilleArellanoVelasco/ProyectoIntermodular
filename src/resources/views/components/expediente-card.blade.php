@props([
    'id',
    'code',
    'title',
    'statusClass',
    'statusLabel',
    'clientName',
    'lawyerName',
    'progress',
    'documents',
])

<div class="card-hover">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2 text-sm font-semibold text-primary-400">
            {{ $code }}
            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
    </div>

    {{-- Title --}}
    <h3 class="text-lg font-semibold text-text-primary mb-4">{{ $title }}</h3>

    {{-- Details --}}
    <div class="flex flex-col gap-2 mb-5">
        <div class="flex items-center gap-2 text-sm text-text-secondary">
            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Cliente: <strong class="text-text-primary">{{ $clientName }}</strong></span>
        </div>

        <div class="flex items-center gap-2 text-sm text-text-secondary">
            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>Gestor: <strong class="text-text-primary">{{ $lawyerName }}</strong></span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="flex justify-between items-center pt-4 border-t border-white/5">
        <div class="flex gap-4">
            <div class="flex items-center gap-2 text-sm text-text-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $documents }}
            </div>
        </div>

        <a href="{{ route('expedientes.show', $id) }}" class="flex items-center gap-2 py-2 px-4 text-primary-400 text-sm font-semibold rounded-lg transition-all duration-200 hover:bg-primary-400/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver
        </a>
    </div>
</div>
