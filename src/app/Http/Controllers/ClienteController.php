<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Consorte;
use App\Models\Empresa;
use App\Models\TipoDocumentacion;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Listado de clientes
     */
    public function index(Request $request)
    {
        $clientes = $this->buildClientesQuery($request)->paginate(15);

        // Si es petición AJAX, devolver solo las filas de la tabla
        if ($request->ajax()) {
            return response()->json([
                'html' => view('clientes.partials.client-rows', compact('clientes'))->render(),
                'hasMore' => $clientes->hasMorePages(),
                'nextPage' => $clientes->currentPage() + 1,
            ]);
        }

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Construir el query base de clientes aplicando los filtros del request.
     * Usado por index() (con paginación) y export() (sin paginación).
     */
    private function buildClientesQuery(Request $request)
    {
        $query = Cliente::with(['empresa', 'consorte'])->withCount('expedientes');

        if ($request->filled('search')) {
            $search = trim($request->search);

            if (str_contains($search, '@')) {
                $query->whereRaw('email ILIKE ?', ["%{$search}%"]);
            } elseif (preg_match('/^[\d\-\.]+[A-Za-z]?$/', $search)) {
                $query->whereRaw('numero_documentacion ILIKE ?', ["%{$search}%"]);
            } else {
                $query->whereRaw(
                    "CONCAT(nombre, ' ', COALESCE(apellido1, ''), ' ', COALESCE(apellido2, '')) ILIKE ?",
                    ["%{$search}%"]
                );
            }
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            if ($request->estado === 'activo') {
                $query->activos();
            } elseif ($request->estado === 'inactivo') {
                $query->inactivos();
            }
        }

        $orderField = $request->get('order', 'fecha');
        $orderDir = in_array($request->get('dir'), ['asc', 'desc']) ? $request->get('dir') : 'desc';

        switch ($orderField) {
            case 'nombre':
                $query->orderBy('nombre', $orderDir)->orderBy('apellido1', $orderDir);
                break;
            case 'expedientes':
                $query->orderBy('expedientes_count', $orderDir);
                break;
            case 'fecha':
            default:
                $query->orderBy('created_at', $orderDir);
                break;
        }

        return $query;
    }

    /**
     * Exportar la lista de clientes (aplicando filtros) a CSV.
     * Exporta TODO el resultado de la consulta, no solo la página actual.
     */
    public function export(Request $request)
    {
        $query = $this->buildClientesQuery($request);

        $filename = 'clientes_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Nombre completo',
            'Nº Documentación',
            'Email',
            'Teléfono',
            'Dirección',
            'Estado',
            'Empresa',
            'Consorte',
            'Expedientes',
            'Creado',
        ];

        return response()->streamDownload(function () use ($query, $headers) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers, ';');

            $query->chunk(500, function ($clientes) use ($out) {
                foreach ($clientes as $c) {
                    fputcsv($out, [
                        $c->nombreCompleto,
                        $c->numero_documentacion ?? '',
                        $c->email ?? '',
                        $c->telefono ?? '',
                        $c->direccion ?? '',
                        $c->estado,
                        $c->empresa?->nombre ?? '',
                        $c->consorte?->nombreCompleto ?? '',
                        $c->expedientes_count,
                        $c->created_at?->format('d/m/Y H:i') ?? '',
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
    public function create()
    {
        $tiposDocumentacion = TipoDocumentacion::all();

        return view('clientes.create', compact('tiposDocumentacion'));
    }

    /**
     * Guardar nuevo cliente
     */
    public function store(Request $request)
    {
        $rules = [
            // Datos del cliente
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'tipo_documentacion_id' => 'required|exists:tipos_documentacion,id',
            'numero_documentacion' => 'required|string|unique:clientes,numero_documentacion',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            // Empresa
            'es_empresa' => 'nullable|boolean',
            'empresa_nombre' => 'required_if:es_empresa,1|nullable|string|max:255',
            'empresa_cif' => 'required_if:es_empresa,1|nullable|string|max:20',
            'empresa_email' => 'nullable|email|max:255',
            'empresa_telefono' => 'nullable|string|max:50',
            'empresa_direccion' => 'nullable|string',
            // Consorte
            'tiene_consorte' => 'nullable|boolean',
            'consorte_nombre' => 'required_if:tiene_consorte,1|nullable|string|max:255',
            'consorte_apellido1' => 'required_if:tiene_consorte,1|nullable|string|max:255',
            'consorte_apellido2' => 'nullable|string|max:255',
            'consorte_tipo_documentacion_id' => 'required_if:tiene_consorte,1|nullable|exists:tipos_documentacion,id',
            'consorte_numero_documentacion' => 'required_if:tiene_consorte,1|nullable|string|unique:consortes,numero_documentacion',
            'consorte_email' => 'nullable|email|max:255',
            'consorte_telefono' => 'nullable|string|max:50',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $empresaId = null;

            // Si es empresa, buscar o crear
            if ($request->boolean('es_empresa') && $request->filled('empresa_cif')) {
                $empresa = Empresa::firstOrCreate(
                    ['cif' => strtoupper($request->empresa_cif)],
                    [
                        'nombre' => $request->empresa_nombre,
                        'email' => $request->empresa_email,
                        'telefono' => $request->empresa_telefono,
                        'direccion' => $request->empresa_direccion,
                    ]
                );
                $empresaId = $empresa->id;
            }

            // Crear cliente
            $cliente = Cliente::create([
                'empresa_id' => $empresaId,
                'nombre' => $validated['nombre'],
                'apellido1' => $validated['apellido1'],
                'apellido2' => $validated['apellido2'] ?? null,
                'tipo_documentacion_id' => $validated['tipo_documentacion_id'],
                'numero_documentacion' => $validated['numero_documentacion'],
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
            ]);

            // Si tiene consorte, crearlo
            if ($request->boolean('tiene_consorte') && $request->filled('consorte_nombre')) {
                Consorte::create([
                    'cliente_id' => $cliente->id,
                    'nombre' => $validated['consorte_nombre'],
                    'apellido1' => $validated['consorte_apellido1'],
                    'apellido2' => $validated['consorte_apellido2'] ?? null,
                    'tipo_documentacion_id' => $validated['consorte_tipo_documentacion_id'],
                    'numero_documentacion' => $validated['consorte_numero_documentacion'],
                    'email' => $validated['consorte_email'] ?? null,
                    'telefono' => $validated['consorte_telefono'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('clientes.show', $cliente)
                ->with('success', 'Cliente creado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e); // Registra el error en los logs para debugging
            return back()->withInput()->with('error', 'Error al crear el cliente. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Ver detalle de cliente
     */
    public function show(Cliente $cliente)
    {
        $cliente->load(['consorte.tipoDocumentacion', 'empresa', 'expedientes.estado', 'expedientes.tipoProcedimiento', 'expedientes.gestor', 'documentos.tipoDocumento', 'documentos.expedientes']);

        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();

        return view('clientes.detalles.cliente-detalles', compact('cliente', 'tiposDocumento'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Cliente $cliente)
    {
        $cliente->load(['empresa', 'consorte']);
        $tiposDocumentacion = TipoDocumentacion::all();

        return view('clientes.edit', compact('cliente', 'tiposDocumentacion'));
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente)
    {
        $cliente->load('consorte');

        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'tipo_documentacion_id' => 'required|exists:tipos_documentacion,id',
            'numero_documentacion' => [
                'required', 'string',
                Rule::unique('clientes', 'numero_documentacion')->ignore($cliente->id),
            ],
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            // Empresa
            'es_empresa' => 'nullable|boolean',
            'empresa_nombre' => 'required_if:es_empresa,1|nullable|string|max:255',
            'empresa_cif' => 'required_if:es_empresa,1|nullable|string|max:20',
            'empresa_email' => 'nullable|email|max:255',
            'empresa_telefono' => 'nullable|string|max:50',
            'empresa_direccion' => 'nullable|string',
            // Consorte
            'tiene_consorte' => 'nullable|boolean',
            'consorte_nombre' => 'required_if:tiene_consorte,1|nullable|string|max:255',
            'consorte_apellido1' => 'required_if:tiene_consorte,1|nullable|string|max:255',
            'consorte_apellido2' => 'nullable|string|max:255',
            'consorte_tipo_documentacion_id' => 'required_if:tiene_consorte,1|nullable|exists:tipos_documentacion,id',
            'consorte_numero_documentacion' => [
                'required_if:tiene_consorte,1', 'nullable', 'string',
                Rule::unique('consortes', 'numero_documentacion')->ignore($cliente->consorte?->id),
            ],
            'consorte_email' => 'nullable|email|max:255',
            'consorte_telefono' => 'nullable|string|max:50',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $empresaId = null;

            if ($request->boolean('es_empresa') && $request->filled('empresa_cif')) {
                $empresa = Empresa::updateOrCreate(
                    ['cif' => strtoupper($request->empresa_cif)],
                    [
                        'nombre' => $request->empresa_nombre,
                        'email' => $request->empresa_email,
                        'telefono' => $request->empresa_telefono,
                        'direccion' => $request->empresa_direccion,
                    ]
                );
                $empresaId = $empresa->id;
            }

            $cliente->update([
                'empresa_id' => $empresaId,
                'nombre' => $validated['nombre'],
                'apellido1' => $validated['apellido1'],
                'apellido2' => $validated['apellido2'] ?? null,
                'tipo_documentacion_id' => $validated['tipo_documentacion_id'],
                'numero_documentacion' => $validated['numero_documentacion'],
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
            ]);

            if ($request->boolean('tiene_consorte') && $request->filled('consorte_nombre')) {
                Consorte::updateOrCreate(
                    ['cliente_id' => $cliente->id],
                    [
                        'nombre' => $validated['consorte_nombre'],
                        'apellido1' => $validated['consorte_apellido1'],
                        'apellido2' => $validated['consorte_apellido2'] ?? null,
                        'tipo_documentacion_id' => $validated['consorte_tipo_documentacion_id'],
                        'numero_documentacion' => $validated['consorte_numero_documentacion'],
                        'email' => $validated['consorte_email'] ?? null,
                        'telefono' => $validated['consorte_telefono'] ?? null,
                    ]
                );
            } elseif ($cliente->consorte) {
                $cliente->consorte->delete();
            }

            DB::commit();

            return redirect()->route('clientes.show', $cliente)
                ->with('success', 'Cliente actualizado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->with('error', 'Error al actualizar el cliente. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Eliminar cliente
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes')
            ->with('success', 'Cliente eliminado correctamente');
    }
}
