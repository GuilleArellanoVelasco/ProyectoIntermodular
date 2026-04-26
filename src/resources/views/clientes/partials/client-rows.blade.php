@foreach ($clientes as $cliente)
    <x-client-item
        :id="$cliente->id"
        :name="$cliente->nombre_completo"
        :initials="$cliente->iniciales"
        :avatar-color="$cliente->avatar_color"
        :dni="$cliente->numero_documentacion"
        :phone="$cliente->telefono ?? 'Sin teléfono'"
        :email="$cliente->email ?? 'Sin email'"
        :status="$cliente->estado_slug"
        :cases-count="$cliente->expedientes_count ?? $cliente->expedientes->count()"
    />
@endforeach
