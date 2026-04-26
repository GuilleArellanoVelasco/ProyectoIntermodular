<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /**
     * Subir un documento asociado a un cliente o expediente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'contexto_tipo' => 'required|in:cliente,expediente',
            'contexto_id' => 'required|integer',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.file' => 'El archivo no es válido.',
            'archivo.mimes' => 'Solo se permiten archivos PDF, Word y Excel.',
            'archivo.max' => 'El archivo no puede superar los 10 MB.',
            'tipo_documento_id.required' => 'Debes seleccionar un tipo de documento.',
            'tipo_documento_id.exists' => 'El tipo de documento no es válido.',
        ]);

        $contextoTipo = $validated['contexto_tipo'];
        $contextoId = $validated['contexto_id'];

        $contexto = $contextoTipo === 'cliente'
            ? Cliente::find($contextoId)
            : Expediente::find($contextoId);

        if (!$contexto) {
            return back()->with('error', 'El ' . $contextoTipo . ' asociado no existe.');
        }

        $archivo = $request->file('archivo');

        DB::transaction(function () use ($archivo, $validated, $contextoTipo, $contextoId) {
            $ruta = $archivo->store('documentos/' . $contextoTipo . '/' . $contextoId, 'local');

            $documento = Documento::create([
                'tipo_documento_id' => $validated['tipo_documento_id'],
                'uploaded_by' => Auth::id(),
                'nombre' => $archivo->getClientOriginalName(),
                'ruta' => $ruta,
                'mime_type' => $archivo->getMimeType(),
                'tamanio' => $archivo->getSize(),
            ]);

            if ($contextoTipo === 'cliente') {
                $documento->clientes()->attach($contextoId);
            } else {
                $documento->expedientes()->attach($contextoId);
            }
        });

        $ruta = $contextoTipo === 'cliente'
            ? route('clientes.show', $contextoId)
            : route('expedientes.show', $contextoId);

        return redirect($ruta)->with('success', 'Documento subido correctamente.');
    }

    /**
     * Descargar un documento
     */
    public function download(Documento $documento)
    {
        if (!Storage::disk('local')->exists($documento->ruta)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->download($documento->ruta, $documento->nombre);
    }

    /**
     * Ver un documento inline (en el navegador)
     */
    public function view(Documento $documento)
    {
        if (!Storage::disk('local')->exists($documento->ruta)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->response($documento->ruta, $documento->nombre);
    }

    /**
     * Eliminar un documento (borrado físico: archivo + registro + pivot)
     */
    public function destroy(Documento $documento)
    {
        DB::transaction(function () use ($documento) {
            if (Storage::disk('local')->exists($documento->ruta)) {
                Storage::disk('local')->delete($documento->ruta);
            }

            $documento->clientes()->detach();
            $documento->expedientes()->detach();
            $documento->delete();
        });

        return back()->with('success', 'Documento eliminado correctamente.');
    }
}
