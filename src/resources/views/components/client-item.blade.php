@props([
    'id',
    'name',
    'initials',
    'avatarColor' => null,
    'dni',
    'phone',
    'email',
    'status',
    'casesCount',
])

<tr>
    <td>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-semibold text-sm text-white shrink-0 {{ $avatarColor ?? 'bg-primary-400' }}">
                {{ $initials }}
            </div>
            <div>
                <div class="font-semibold text-text-primary">{{ $name }}</div>
            </div>
        </div>
    </td>

    <td>
        <span class="text-text-secondary">{{ $dni }}</span>
    </td>

    <td>
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 text-sm text-text-secondary">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>{{ $phone }}</span>
            </div>

            <div class="flex items-center gap-2 text-sm text-text-secondary">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>{{ $email }}</span>
            </div>
        </div>
    </td>

    <td>
        <span class="badge badge-{{ $status }}">
            {{ ucfirst($status) }}
        </span>
    </td>

    <td>
        <div class="flex items-center gap-2 text-text-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            {{ $casesCount }}
        </div>
    </td>

    <td>
        <div class="table-actions">
            <a href="{{ route('clientes.show', $id) }}" class="action-icon" title="Ver">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                           -1.274 4.057-5.064 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>

            <a href="{{ route('clientes.edit', $id) }}" class="action-icon" title="Editar">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        </div>
    </td>
</tr>
