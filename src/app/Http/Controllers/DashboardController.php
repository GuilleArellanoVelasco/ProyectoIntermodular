<?php

namespace App\Http\Controllers;

use App\Models\EventoCalendario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $fechaSeleccionada = $request->filled('fecha')
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        $eventosDelDia = EventoCalendario::activos()
            ->delUsuario($userId)
            ->enFecha($fechaSeleccionada)
            ->with('expediente:id,numero_expediente')
            ->orderBy('tipo')
            ->get();

        $mesInicio = $fechaSeleccionada->copy()->startOfMonth();
        $mesFin = $fechaSeleccionada->copy()->endOfMonth();

        $eventosDelMes = EventoCalendario::activos()
            ->delUsuario($userId)
            ->whereBetween('fecha', [$mesInicio->toDateString(), $mesFin->toDateString()])
            ->get(['id', 'fecha', 'tipo', 'titulo'])
            ->map(fn ($e) => [
                'day' => $e->fecha->day,
                'priority' => $e->tipo === 'alerta' ? 'media' : 'baja',
                'title' => $e->titulo,
            ])
            ->values()
            ->all();

        return view('dashboard', compact('eventosDelDia', 'eventosDelMes', 'fechaSeleccionada'));
    }
}
