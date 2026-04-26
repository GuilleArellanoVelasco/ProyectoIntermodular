<?php

namespace App\Http\Controllers;

use App\Models\FacturaHonorarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturaHonorariosController extends Controller
{
    public function subirPdf(Request $request, FacturaHonorarios $factura)
    {
        if ($factura->tiene_pdf) {
            return back()->with('error', 'Esta factura ya tiene un PDF asociado.');
        }

        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.file' => 'El archivo no es válido.',
            'archivo.mimes' => 'Solo se permiten archivos PDF.',
            'archivo.max' => 'El archivo no puede superar los 10 MB.',
        ]);

        $expedienteId = $factura->cuota->plan->expediente_id;
        $archivo = $request->file('archivo');

        $ruta = $archivo->store('facturas/' . $expedienteId, 'local');

        $factura->update([
            'documento_pdf_ruta' => $ruta,
            'documento_pdf_nombre' => $archivo->getClientOriginalName(),
            'documento_pdf_subido_at' => now(),
        ]);

        return redirect()
            ->route('expedientes.show', $expedienteId)
            ->with('success', 'Factura subida correctamente.');
    }

    public function descargar(FacturaHonorarios $factura)
    {
        if (!$factura->tiene_pdf || !Storage::disk('local')->exists($factura->documento_pdf_ruta)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->download($factura->documento_pdf_ruta, $factura->documento_pdf_nombre);
    }
}
