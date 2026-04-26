<?php

namespace App\Http\Controllers;

use App\Models\CuotaHonorarios;
use App\Models\Expediente;
use App\Models\FacturaHonorarios;
use App\Models\PlanPagoHonorarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanPagoHonorariosController extends Controller
{
    public function store(Request $request, Expediente $expediente)
    {
        $validated = $request->validate([
            'importe_total' => 'required|numeric|min:0.01',
            'numero_cuotas' => 'required|integer|min:1|max:120',
            'fecha_primer_vencimiento' => 'required|date',
        ], [
            'importe_total.required' => 'El importe total es obligatorio.',
            'importe_total.numeric' => 'El importe total debe ser un número.',
            'importe_total.min' => 'El importe total debe ser mayor que cero.',
            'numero_cuotas.required' => 'El número de cuotas es obligatorio.',
            'numero_cuotas.integer' => 'El número de cuotas debe ser entero.',
            'numero_cuotas.min' => 'Debe haber al menos una cuota.',
            'numero_cuotas.max' => 'El número máximo de cuotas es 120.',
            'fecha_primer_vencimiento.required' => 'La fecha del primer vencimiento es obligatoria.',
            'fecha_primer_vencimiento.date' => 'La fecha del primer vencimiento no es válida.',
        ]);

        if ($expediente->planPagoHonorarios()->exists()) {
            return back()->with('error', 'El expediente ya tiene un plan de pago de honorarios.');
        }

        $total = round((float) $validated['importe_total'], 2);
        $n = (int) $validated['numero_cuotas'];
        $importeCuota = floor(($total / $n) * 100) / 100;
        $resto = round($total - ($importeCuota * $n), 2);
        $fechaPrimer = Carbon::parse($validated['fecha_primer_vencimiento']);

        DB::transaction(function () use ($expediente, $total, $n, $importeCuota, $resto, $fechaPrimer) {
            $plan = PlanPagoHonorarios::create([
                'expediente_id' => $expediente->id,
                'importe_total' => $total,
                'numero_cuotas' => $n,
                'importe_cuota' => $importeCuota,
                'fecha_primer_vencimiento' => $fechaPrimer,
            ]);

            for ($i = 1; $i <= $n; $i++) {
                $importe = ($i === $n) ? round($importeCuota + $resto, 2) : $importeCuota;
                CuotaHonorarios::create([
                    'plan_pago_honorarios_id' => $plan->id,
                    'numero_cuota' => $i,
                    'fecha_vencimiento' => $fechaPrimer->copy()->addMonths($i - 1),
                    'importe' => $importe,
                    'pagada' => false,
                ]);
            }
        });

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Plan de pago de honorarios creado correctamente.');
    }

    public function destroy(Expediente $expediente, PlanPagoHonorarios $plan)
    {
        if ($plan->expediente_id !== $expediente->id) {
            return back()->with('error', 'El plan no pertenece a este expediente.');
        }

        $plan->delete();

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Plan de pago de honorarios eliminado.');
    }

    public function registrarPago(Request $request, Expediente $expediente)
    {
        $validated = $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,domiciliacion',
        ], [
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'fecha_pago.date' => 'La fecha de pago no es válida.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido.',
        ]);

        $plan = $expediente->planPagoHonorarios;

        if (!$plan) {
            return back()->with('error', 'Este expediente no tiene plan de pago de honorarios.');
        }

        $siguienteCuota = $plan->cuotas()->where('pagada', false)->orderBy('numero_cuota')->first();

        if (!$siguienteCuota) {
            return back()->with('error', 'Todas las cuotas ya están pagadas.');
        }

        DB::transaction(function () use ($siguienteCuota, $validated) {
            $siguienteCuota->update([
                'pagada' => true,
                'fecha_pago' => $validated['fecha_pago'],
                'metodo_pago' => $validated['metodo_pago'],
            ]);

            $year = Carbon::parse($validated['fecha_pago'])->year;
            $ultimaFactura = FacturaHonorarios::whereYear('fecha_emision', $year)
                ->orderByDesc('id')
                ->first();

            $siguienteNumero = $ultimaFactura
                ? ((int) substr($ultimaFactura->numero_factura, -6)) + 1
                : 1;

            $numeroFactura = sprintf('F-%d-%06d', $year, $siguienteNumero);

            FacturaHonorarios::create([
                'cuota_honorarios_id' => $siguienteCuota->id,
                'numero_factura' => $numeroFactura,
                'fecha_emision' => $validated['fecha_pago'],
                'importe' => $siguienteCuota->importe,
            ]);
        });

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', "Pago de la cuota {$siguienteCuota->numero_cuota}/{$plan->numero_cuotas} registrado correctamente.");
    }
}
