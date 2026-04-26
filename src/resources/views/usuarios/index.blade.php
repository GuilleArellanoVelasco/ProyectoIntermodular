@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')
@section('page-subtitle', 'Administra los usuarios del sistema y sus permisos')

@section('content')
    <div class="animate-fade-in">
        <div class="page-header">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-0">
                <form action="{{ route('usuarios') }}" method="GET" class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" class="search-input"
                        placeholder="Buscar por nombre o email..."
                        value="{{ request('search') }}">
                    @if($mostrarInactivos)
                        <input type="hidden" name="inactivos" value="1">
                    @endif
                </form>
            </div>

            <div class="flex flex-wrap gap-3">
                @if($mostrarInactivos)
                    <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ver activos
                    </a>
                @else
                    <a href="{{ route('usuarios', ['inactivos' => 1]) }}" class="btn btn-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        Ver inactivos
                    </a>
                @endif

                @unless($mostrarInactivos)
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Usuario
                    </a>
                @endunless
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h3 class="section-title">
                    {{ $usuarios->total() }} {{ Str::plural('Usuario', $usuarios->total()) }}
                    @if($mostrarInactivos)
                        <span class="text-sm font-normal text-text-muted">(inactivos)</span>
                    @endif
                </h3>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-semibold text-sm text-white shrink-0 {{ $u->avatarColor }}">
                                            {{ $u->iniciales }}
                                        </div>
                                        <div>
                                            <strong class="text-text-primary">{{ $u->nombreCompleto }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-text-secondary">{{ $u->email }}</td>
                                <td>
                                    @php $rol = $u->roles->first(); @endphp
                                    @if($rol)
                                        <span class="badge {{ $rol->name === 'admin' ? 'badge-proceso' : 'badge-activo' }}">
                                            {{ $rol->display_name }}
                                        </span>
                                    @else
                                        <span class="text-text-muted text-xs">Sin rol</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->trashed())
                                        <span class="badge badge-archivado">Desactivado</span>
                                    @elseif($u->is_active)
                                        <span class="badge badge-activo">Activo</span>
                                    @else
                                        <span class="badge badge-pendiente">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-text-secondary">
                                    {{ $u->last_login_at ? $u->last_login_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td>
                                    <div class="table-actions">
                                        @if($u->trashed())
                                            <form method="POST" action="{{ route('usuarios.restore', $u->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Reactivar usuario">
                                                    Reactivar
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('usuarios.edit', $u) }}" class="action-icon" title="Editar">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @if($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('usuarios.destroy', $u) }}" class="inline"
                                                      onsubmit="return confirm('¿Desactivar este usuario? Podrás reactivarlo después desde la pestaña de inactivos.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-icon text-error" title="Desactivar">
                                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-12">
                                        <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <h4 class="empty-state-title">{{ $mostrarInactivos ? 'No hay usuarios inactivos' : 'No hay usuarios' }}</h4>
                                        @unless($mostrarInactivos)
                                            <p class="empty-state-description">Crea el primer usuario para empezar.</p>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($usuarios->hasPages())
                <div class="mt-4">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
