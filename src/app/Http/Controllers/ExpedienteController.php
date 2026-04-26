<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EventoCalendario;
use App\Models\Expediente;
use App\Models\EstadoExpediente;
use App\Models\HistorialProcesoExpediente;
use App\Models\TipoDocumento;
use App\Models\TipoProcedimiento;
use App\Models\TransicionProceso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    /**
     * Listado de expedientes
     */
    public function index(Request $request)
    {
        $expedientes = $this->buildExpedientesQuery($request)->paginate(12);

        // Si es petición AJAX, devolver solo las cards
        if ($request->ajax()) {
            return response()->json([
                'html' => view('expedientes.partials.expediente-cards', compact('expedientes'))->render(),
                'hasMore' => $expedientes->hasMorePages(),
                'nextPage' => $expedientes->currentPage() + 1,
            ]);
        }

        $estados = EstadoExpediente::all();
        $tiposProcedimiento = TipoProcedimiento::all();

        return view('expedientes.index', compact('expedientes', 'estados', 'tiposProcedimiento'));
    }

    /**
     * Construir el query base de expedientes aplicando los filtros del request.
     * Usado por index() (con paginación) y export() (sin paginación).
     */
    private function buildExpedientesQuery(Request $request)
    {
        $query = Expediente::with(['cliente', 'estado', 'tipoProcedimiento', 'gestor'])
            ->withCount('documentos');

        if ($request->filled('estado') && $request->estado !== 'todos') {
            if ($request->estado === 'abiertos') {
                $query->whereHas('estado', fn($q) => $q->where('estado', '!=', 'Archivado'));
            } elseif ($request->estado === 'pendiente_accion') {
                $query->whereHas('estado', fn($q) => $q->where('estado', 'Pendiente de acción'));
            } elseif ($request->estado === 'archivados') {
                $query->whereHas('estado', fn($q) => $q->where('estado', 'Archivado'));
            }
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo_procedimiento_id', $request->tipo);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            if (preg_match('/^EXP-/i', $search)) {
                $query->whereRaw('numero_expediente ILIKE ?', ['%' . $search . '%']);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('cliente', function ($q) use ($search) {
                        $q->whereRaw(
                            "CONCAT(nombre, ' ', COALESCE(apellido1, ''), ' ', COALESCE(apellido2, '')) ILIKE ?",
                            ["%{$search}%"]
                        );
                    })->orWhereHas('gestor', function ($q) use ($search) {
                        $q->whereRaw(
                            "CONCAT(nombre, ' ', COALESCE(apellido1, ''), ' ', COALESCE(apellido2, '')) ILIKE ?",
                            ["%{$search}%"]
                        );
                    });
                });
            }
        }

        $orderDir = in_array($request->get('dir'), ['asc', 'desc']) ? $request->get('dir') : 'desc';
        $query->orderBy('created_at', $orderDir);

        return $query;
    }

    /**
     * Exportar la lista de expedientes (aplicando filtros) a CSV.
     * Exporta TODO el resultado de la consulta, no solo la página actual.
     */
    public function export(Request $request)
    {
        $query = $this->buildExpedientesQuery($request);

        $filename = 'expedientes_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Nº Expediente',
            'Cliente',
            'DNI Cliente',
            'Gestor',
            'Tipo de procedimiento',
            'Estado',
            'Fecha apertura',
            'Fecha cierre',
            'Publicación BOE',
            'Publicación RPC',
            'Documentos',
            'Creado',
        ];

        return response()->streamDownload(function () use ($query, $headers) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 para que Excel reconozca los acentos
            fwrite($out, "\xEF\xBB\xBF");
            // Separador ; para compatibilidad con Excel en español
            fputcsv($out, $headers, ';');

            $query->chunk(500, function ($expedientes) use ($out) {
                foreach ($expedientes as $e) {
                    fputcsv($out, [
                        $e->numero_expediente,
                        $e->cliente?->nombreCompleto ?? '',
                        $e->cliente?->numero_documentacion ?? '',
                        $e->gestor?->nombreCompleto ?? '',
                        $e->tipoProcedimiento?->nombre ?? '',
                        $e->estado?->estado ?? '',
                        $e->fecha_apertura?->format('d/m/Y') ?? '',
                        $e->fecha_cierre?->format('d/m/Y') ?? '',
                        $e->fecha_publicacion_boe?->format('d/m/Y') ?? '',
                        $e->fecha_publicacion_rpc?->format('d/m/Y') ?? '',
                        $e->documentos_count,
                        $e->created_at?->format('d/m/Y H:i') ?? '',
                    ], ';');
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $gestores = User::active()->orderBy('nombre')->get();
        $tiposProcedimiento = TipoProcedimiento::all();

        $clienteIdPreseleccionado = $request->query('cliente_id');
        if ($clienteIdPreseleccionado && !$clientes->contains('id', (int) $clienteIdPreseleccionado)) {
            $clienteIdPreseleccionado = null;
        }

        return view('expedientes.create', compact(
            'clientes',
            'gestores',
            'tiposProcedimiento',
            'clienteIdPreseleccionado'
        ));
    }

    /**
     * Guardar nuevo expediente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'gestor_id' => 'required|exists:users,id',
            'tipo_procedimiento_id' => 'required|exists:tipos_procedimiento,id',
            'tipo_encargo' => 'required_if:tipo_procedimiento_id,3|nullable|string|max:255',
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'gestor_id.required' => 'El gestor es obligatorio.',
            'tipo_procedimiento_id.required' => 'El tipo de procedimiento es obligatorio.',
            'tipo_encargo.required_if' => 'El tipo de encargo es obligatorio para procedimientos de tipo "Otros".',
        ]);

        // Generar numero de expediente
        $ultimoExpediente = Expediente::withTrashed()->orderBy('id', 'desc')->first();
        $siguienteNumero = $ultimoExpediente ? $ultimoExpediente->id + 1 : 1;
        $numeroExpediente = 'EXP-' . str_pad($siguienteNumero, 6, '0', STR_PAD_LEFT);

        // Todos los expedientes nacen como "Abierto" sin importar el tipo.
        // Para LSO (tipos 1 y 2) el estado pasa a "Pendiente de acción" al iniciar
        // el proceso mediante sincronizarEstado().
        $estadoInicial = EstadoExpediente::where('estado', 'Abierto')->first();

        $expediente = Expediente::create([
            'numero_expediente' => $numeroExpediente,
            'cliente_id' => $validated['cliente_id'],
            'gestor_id' => $validated['gestor_id'],
            'tipo_procedimiento_id' => $validated['tipo_procedimiento_id'],
            'estado_id' => $estadoInicial?->id,
            'fecha_apertura' => now(),
        ]);

        // Si es tipo "Otros" (ID 3), guardar el tipo de encargo
        if ($validated['tipo_procedimiento_id'] == 3 && !empty($validated['tipo_encargo'])) {
            $expediente->otros()->create([
                'tipo_encargo' => $validated['tipo_encargo'],
            ]);
        }

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Expediente creado correctamente.');
    }

    /**
     * Ver detalle de expediente
     */
    public function show(Expediente $expediente)
    {
        $expediente->load([
            'cliente.consorte',
            'estado',
            'tipoProcedimiento',
            'gestor',
            'documentos.tipoDocumento',
            'tareas',
            'estadoProceso',
            'historialProceso.estado',
            'historialProceso.usuario',
            'planPagoHonorarios.cuotas.factura',
            'planPagosAcreedores',
        ]);

        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();

        return view('expedientes.detalles.expediente-detalles', compact('expediente', 'tiposDocumento'));
    }

    /**
     * Iniciar el seguimiento del proceso del expediente
     */
    public function iniciarProceso(Expediente $expediente)
    {
        if ($expediente->fecha_cierre) {
            return back()->with('error', 'No se puede iniciar el proceso de un expediente archivado.');
        }

        if (!$expediente->tieneSeguimientoProceso()) {
            return back()->with('error', 'Este tipo de expediente no tiene seguimiento de proceso.');
        }

        if ($expediente->estado_proceso_id) {
            return back()->with('error', 'El proceso ya ha sido iniciado.');
        }

        if ($expediente->iniciarProceso()) {
            return back()->with('success', 'Proceso iniciado correctamente.');
        }

        return back()->with('error', 'No se pudo iniciar el proceso.');
    }

    /**
     * Avanzar al siguiente estado del proceso
     */
    public function avanzarProceso(Request $request, Expediente $expediente)
    {
        if ($expediente->fecha_cierre) {
            return back()->with('error', 'No se puede modificar el proceso de un expediente archivado.');
        }

        $request->validate([
            'transicion_id' => 'required|exists:transiciones_proceso,id',
        ]);

        $transicion = TransicionProceso::find($request->transicion_id);

        // Verificar que la transición es válida para el estado actual
        if (!$transicion || $transicion->estado_origen_id !== $expediente->estado_proceso_id) {
            return back()->with('error', 'Transición no válida desde el estado actual.');
        }

        if ($expediente->avanzarEstado($request->transicion_id)) {
            return back()->with('success', 'Estado actualizado a: ' . $transicion->estadoDestino->nombre);
        }

        return back()->with('error', 'No se pudo avanzar el proceso.');
    }

    /**
     * Registrar fecha de publicacion BOE o RPC
     */
    public function registrarPublicacion(Request $request, Expediente $expediente)
    {
        if ($expediente->fecha_cierre) {
            return back()->with('error', 'No se puede modificar el proceso de un expediente archivado.');
        }

        $request->validate([
            'tipo' => 'required|in:boe,rpc',
            'fecha' => 'required|date',
        ]);

        if (!$expediente->tieneSeguimientoProceso()) {
            return back()->with('error', 'Este tipo de expediente no tiene seguimiento de proceso.');
        }

        $tipo = $request->tipo;
        $tipoLabel = strtoupper($tipo);

        // Verificar si es la primera publicacion (para avanzar el proceso)
        $datosPublicaciones = $expediente->getDatosPublicaciones();
        $esPrimeraPublicacion = is_null($datosPublicaciones['fecha_publicacion_boe'])
            && is_null($datosPublicaciones['fecha_publicacion_rpc']);

        if ($expediente->registrarPublicacion($tipo, $request->fecha)) {
            $mensaje = "Fecha de publicacion {$tipoLabel} registrada correctamente.";

            // Si es la primera publicacion y estamos en "esperando_publicaciones", avanzar automaticamente
            if ($esPrimeraPublicacion && $expediente->estadoProceso && $expediente->estadoProceso->codigo === 'esperando_publicaciones') {
                // Buscar la transicion a "periodo_alegaciones"
                $transicion = TransicionProceso::where('estado_origen_id', $expediente->estado_proceso_id)
                    ->whereHas('estadoDestino', function ($q) {
                        $q->where('codigo', 'periodo_alegaciones');
                    })
                    ->first();

                if ($transicion) {
                    $expediente->avanzarEstado($transicion->id, "Primera publicacion ({$tipoLabel}) registrada - inicio del periodo de alegaciones");
                    $mensaje .= " El periodo de alegaciones ha comenzado.";
                }
            }

            return back()->with('success', $mensaje);
        }

        return back()->with('error', "No se pudo registrar la fecha de publicacion {$tipoLabel}.");
    }

    /**
     * Archivar manualmente el expediente.
     * Funciona para cualquier tipo de expediente que no esté ya archivado.
     * Cierra el historial de proceso abierto y las alertas de calendario pendientes.
     */
    public function archivar(Expediente $expediente)
    {
        if ($expediente->fecha_cierre) {
            return back()->with('error', 'El expediente ya está archivado.');
        }

        DB::transaction(function () use ($expediente) {
            HistorialProcesoExpediente::where('expediente_id', $expediente->id)
                ->whereNull('fecha_salida')
                ->update(['fecha_salida' => now()]);

            EventoCalendario::where('expediente_id', $expediente->id)
                ->whereNull('resuelto_at')
                ->update(['resuelto_at' => now()]);

            $expediente->fecha_cierre = now();
            $expediente->save();

            $expediente->sincronizarEstado();
        });

        return back()->with('success', 'Expediente archivado correctamente.');
    }

    /**
     * Actualizar fecha de un registro del historial de proceso
     */
    public function actualizarFechaHistorial(Request $request, Expediente $expediente, HistorialProcesoExpediente $historial)
    {
        // Verificar que el historial pertenece al expediente
        if ($historial->expediente_id !== $expediente->id) {
            return back()->with('error', 'El registro no pertenece a este expediente.');
        }

        $request->validate([
            'fecha_entrada' => 'required|date',
        ]);

        $historial->fecha_entrada = $request->fecha_entrada;

        if ($historial->save()) {
            return back()->with('success', 'Fecha actualizada correctamente.');
        }

        return back()->with('error', 'No se pudo actualizar la fecha.');
    }
}
