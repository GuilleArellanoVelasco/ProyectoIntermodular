<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\PlanPagosAcreedor;
use Illuminate\Http\Request;

class PlanPagoAcreedoresController extends Controller
{
    public function store(Request $request, Expediente $expediente)
    {
        $validated = $request->validate([
            'nombre_acreedor' => 'required|string|max:255',
            'deuda_original' => 'required|numeric|min:0.01',
            'propuesta' => 'required|numeric|min:0|lte:deuda_original',
        ], [
            'nombre_acreedor.required' => 'El nombre del acreedor es obligatorio.',
            'deuda_original.required' => 'La deuda original es obligatoria.',
            'deuda_original.numeric' => 'La deuda original debe ser un número.',
            'deuda_original.min' => 'La deuda original debe ser mayor que cero.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.numeric' => 'La propuesta debe ser un número.',
            'propuesta.min' => 'La propuesta no puede ser negativa.',
            'propuesta.lte' => 'La propuesta no puede superar la deuda original.',
        ]);

        $expediente->planPagosAcreedores()->create([
            'nombre_acreedor' => $validated['nombre_acreedor'],
            'deuda_original' => $validated['deuda_original'],
            'propuesta' => $validated['propuesta'],
        ]);

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Acreedor añadido al plan de pagos.');
    }

    public function update(Request $request, Expediente $expediente, PlanPagosAcreedor $linea)
    {
        if ($linea->expediente_id !== $expediente->id) {
            return back()->with('error', 'La línea no pertenece a este expediente.');
        }

        if ($linea->pagado) {
            return back()->with('error', 'No se puede editar una línea ya pagada.');
        }

        $validated = $request->validate([
            'deuda_original' => 'required|numeric|min:0.01',
            'propuesta' => 'required|numeric|min:0|lte:deuda_original',
        ], [
            'deuda_original.required' => 'La deuda original es obligatoria.',
            'deuda_original.numeric' => 'La deuda original debe ser un número.',
            'deuda_original.min' => 'La deuda original debe ser mayor que cero.',
            'propuesta.required' => 'La propuesta es obligatoria.',
            'propuesta.numeric' => 'La propuesta debe ser un número.',
            'propuesta.min' => 'La propuesta no puede ser negativa.',
            'propuesta.lte' => 'La propuesta no puede superar la deuda original.',
        ]);

        $linea->update($validated);

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Línea del plan de pagos actualizada.');
    }

    public function registrarPago(Request $request, Expediente $expediente, PlanPagosAcreedor $linea)
    {
        if ($linea->expediente_id !== $expediente->id) {
            return back()->with('error', 'La línea no pertenece a este expediente.');
        }

        if ($linea->pagado) {
            return back()->with('error', 'Esta línea ya está marcada como pagada.');
        }

        $validated = $request->validate([
            'fecha_pago' => 'required|date',
        ], [
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'fecha_pago.date' => 'La fecha de pago no es válida.',
        ]);

        $linea->update([
            'pagado' => true,
            'fecha_pago' => $validated['fecha_pago'],
        ]);

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', "Pago de {$linea->nombre_acreedor} registrado correctamente.");
    }
}
