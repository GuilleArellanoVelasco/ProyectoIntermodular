<?php

namespace App\Http\Controllers;

use App\Models\EventoCalendario;
use Illuminate\Http\Request;

class EventoCalendarioController extends Controller
{
    /**
     * Crear un recordatorio personal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date|after_or_equal:today',
            'descripcion' => 'nullable|string',
        ]);

        EventoCalendario::create([
            'user_id' => auth()->id(),
            'tipo' => 'recordatorio',
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'fecha' => $validated['fecha'],
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Recordatorio creado correctamente');
    }

    /**
     * Marcar un evento como resuelto (aplicable a recordatorios y alertas del propio usuario)
     */
    public function update(Request $request, EventoCalendario $evento)
    {
        abort_if($evento->user_id !== auth()->id(), 403);
        abort_if($evento->tipo !== 'recordatorio', 403, 'Las alertas se resuelven automáticamente al avanzar de estado.');

        $evento->update(['resuelto_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Recordatorio marcado como resuelto');
    }

    /**
     * Eliminar un recordatorio propio (las alertas no se borran manualmente)
     */
    public function destroy(EventoCalendario $evento)
    {
        abort_if($evento->user_id !== auth()->id(), 403);
        abort_if($evento->tipo !== 'recordatorio', 403, 'Solo se pueden eliminar recordatorios.');

        $evento->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Recordatorio eliminado');
    }
}
