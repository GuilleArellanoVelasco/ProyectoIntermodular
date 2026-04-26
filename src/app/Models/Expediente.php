<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Expediente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_expediente',
        'cliente_id',
        'gestor_id',
        'tipo_procedimiento_id',
        'fecha_apertura',
        'fecha_cierre',
        'estado_id',
        'estado_proceso_id',
        'fecha_publicacion_boe',
        'fecha_publicacion_rpc',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
        'fecha_cierre' => 'date',
        'fecha_publicacion_boe' => 'date',
        'fecha_publicacion_rpc' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function tipoProcedimiento()
    {
        return $this->belongsTo(TipoProcedimiento::class, 'tipo_procedimiento_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoExpediente::class, 'estado_id');
    }

    public function documentos()
    {
        return $this->belongsToMany(Documento::class, 'documento_expediente');
    }

    // ============================================
    // RELACIONES DE HERENCIA (TIPO DE PROCEDIMIENTO)
    // Un expediente ES de un tipo específico, determinado por tipo_procedimiento_id:
    // - tipo 1: LSO sin masa (datos en historial de proceso + fechas publicación en expedientes)
    // - tipo 2: LSO con plan de pagos (datos en historial de proceso + fechas publicación en expedientes)
    // - tipo 3: Otros (tiene tabla propia con campos específicos)
    // ============================================

    public function otros()
    {
        return $this->hasOne(Otro::class);
    }

    /**
     * Obtener los detalles específicos del tipo de procedimiento
     * Solo aplica al tipo 3 (Otros) que tiene campos únicos
     */
    public function getDetallesProcedimientoAttribute()
    {
        return $this->tipo_procedimiento_id === 3 ? $this->otros : null;
    }

    /**
     * Líneas del plan de pagos a acreedores.
     * Cada línea es un acreedor con su deuda original y la propuesta a pagar.
     */
    public function planPagosAcreedores()
    {
        return $this->hasMany(PlanPagosAcreedor::class);
    }

    /**
     * Plan de pago de honorarios del expediente (uno por expediente)
     */
    public function planPagoHonorarios()
    {
        return $this->hasOne(PlanPagoHonorarios::class);
    }

    // ============================================
    // SISTEMA DE AUTÓMATA DE ESTADOS
    // Cada tipo de procedimiento tiene su propio autómata:
    // - LSO sin masa (tipo_procedimiento_id = 1)
    // - LSO con plan de pagos (tipo_procedimiento_id = 2)
    // - Otros (tipo_procedimiento_id = 3) - sin seguimiento
    // ============================================

    public function estadoProceso()
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_proceso_id');
    }

    public function historialProceso()
    {
        return $this->hasMany(HistorialProcesoExpediente::class)
            ->orderBy('fecha_entrada', 'desc');
    }

    /**
     * Obtener las transiciones disponibles desde el estado actual.
     * Un expediente archivado no tiene transiciones disponibles.
     */
    public function getTransicionesDisponiblesAttribute()
    {
        if ($this->fecha_cierre || !$this->estadoProceso) {
            return collect();
        }

        return $this->estadoProceso->transicionesSalientes()
            ->with('estadoDestino')
            ->get();
    }

    /**
     * Verificar si el tipo de procedimiento tiene autómata de estados
     * Solo LSO sin masa (1) y LSO con plan (2) lo tienen
     */
    public function tieneSeguimientoProceso(): bool
    {
        return in_array($this->tipo_procedimiento_id, [1, 2]);
    }

    /**
     * Obtener todos los estados del autómata de este tipo de procedimiento
     */
    public function getEstadosAutomataAttribute()
    {
        return EstadoProceso::where('tipo_procedimiento_id', $this->tipo_procedimiento_id)
            ->orderBy('orden')
            ->get();
    }

    /**
     * Iniciar el seguimiento del proceso (establecer estado inicial)
     * Busca el estado inicial específico para el tipo de procedimiento de este expediente
     */
    public function iniciarProceso(?int $usuarioId = null): bool
    {
        if (!$this->tieneSeguimientoProceso()) {
            return false;
        }

        if ($this->fecha_cierre) {
            return false; // Expediente archivado
        }

        if ($this->estado_proceso_id) {
            return false; // Ya tiene proceso iniciado
        }

        // Obtener el estado inicial del autómata de ESTE tipo de procedimiento
        $estadoInicial = EstadoProceso::where('tipo_procedimiento_id', $this->tipo_procedimiento_id)
            ->where('es_inicial', true)
            ->first();

        if (!$estadoInicial) {
            return false;
        }

        DB::transaction(function () use ($estadoInicial, $usuarioId) {
            $this->estado_proceso_id = $estadoInicial->id;
            $this->save();

            HistorialProcesoExpediente::create([
                'expediente_id' => $this->id,
                'estado_id' => $estadoInicial->id,
                'transicion_id' => null,
                'usuario_id' => $usuarioId ?? Auth::id(),
                'fecha_entrada' => now(),
            ]);

            $this->crearAlertaSiRequiereAccion($estadoInicial);

            // Sincronizar el estado del expediente
            $this->refresh();
            $this->sincronizarEstado();
        });

        return true;
    }

    /**
     * Avanzar al siguiente estado mediante una transición
     */
    public function avanzarEstado(int $transicionId, ?int $usuarioId = null): bool
    {
        if ($this->fecha_cierre) {
            return false; // Expediente archivado
        }

        if (!$this->estadoProceso) {
            return false;
        }

        $transicion = TransicionProceso::find($transicionId);

        if (!$transicion || $transicion->estado_origen_id !== $this->estado_proceso_id) {
            return false; // Transición no válida desde el estado actual
        }

        DB::transaction(function () use ($transicion, $usuarioId) {
            // Cerrar el estado actual en el historial
            HistorialProcesoExpediente::where('expediente_id', $this->id)
                ->whereNull('fecha_salida')
                ->update(['fecha_salida' => now()]);

            // Cerrar cualquier alerta activa del estado anterior
            EventoCalendario::where('expediente_id', $this->id)
                ->where('estado_proceso_id', $transicion->estado_origen_id)
                ->whereNull('resuelto_at')
                ->update(['resuelto_at' => now()]);

            // Actualizar el estado del expediente
            $this->estado_proceso_id = $transicion->estado_destino_id;
            $this->save();

            // Crear nuevo registro en el historial
            HistorialProcesoExpediente::create([
                'expediente_id' => $this->id,
                'estado_id' => $transicion->estado_destino_id,
                'transicion_id' => $transicion->id,
                'usuario_id' => $usuarioId ?? Auth::id(),
                'fecha_entrada' => now(),
            ]);

            // Si el nuevo estado es final, cerrar el expediente
            $nuevoEstado = EstadoProceso::find($transicion->estado_destino_id);
            if ($nuevoEstado && $nuevoEstado->es_final && !$this->fecha_cierre) {
                $this->fecha_cierre = now();
                $this->save();
            }

            if ($nuevoEstado) {
                $this->crearAlertaSiRequiereAccion($nuevoEstado);
            }

            // Sincronizar el estado del expediente
            $this->refresh();
            $this->sincronizarEstado();
        });

        return true;
    }

    /**
     * Crea una alerta en el calendario del gestor si el estado requiere acción
     */
    private function crearAlertaSiRequiereAccion(EstadoProceso $estado): void
    {
        if (!in_array($estado->tipo_accion, ['gestor', 'decision'])) {
            return;
        }

        if (!$this->gestor_id) {
            return;
        }

        EventoCalendario::create([
            'user_id' => $this->gestor_id,
            'tipo' => 'alerta',
            'titulo' => 'Acción requerida: ' . $estado->nombre,
            'descripcion' => $estado->descripcion,
            'fecha' => now()->toDateString(),
            'expediente_id' => $this->id,
            'estado_proceso_id' => $estado->id,
        ]);
    }

    /**
     * Obtener el progreso del proceso como porcentaje
     * Basado en el orden del estado actual vs total de estados no finales
     */
    public function getProgresoProcesoAttribute(): int
    {
        if (!$this->estadoProceso) {
            return 0;
        }

        $totalEstados = EstadoProceso::where('tipo_procedimiento_id', $this->tipo_procedimiento_id)
            ->where('es_final', false)
            ->count();

        if ($totalEstados === 0) {
            return 0;
        }

        $ordenActual = $this->estadoProceso->orden;
        return min(100, round(($ordenActual / $totalEstados) * 100));
    }

    /**
     * Verificar si el proceso ha finalizado, ya sea por haber alcanzado
     * un estado final del autómata o por archivado manual.
     */
    public function procesoFinalizado(): bool
    {
        if ($this->fecha_cierre) {
            return true;
        }

        return $this->estadoProceso && $this->estadoProceso->es_final;
    }

    /**
     * Verdadero si el expediente fue archivado manualmente, es decir,
     * está cerrado pero su estado de proceso (si lo tiene) no es final.
     */
    public function archivadoManualmente(): bool
    {
        if (!$this->fecha_cierre) {
            return false;
        }

        return !$this->estadoProceso || !$this->estadoProceso->es_final;
    }

    /**
     * Obtener el resultado final del proceso (exito/fracaso)
     */
    public function getResultadoProcesoAttribute(): ?string
    {
        if (!$this->procesoFinalizado()) {
            return null;
        }

        return $this->estadoProceso->resultado_final;
    }

    /**
     * Sincronizar el estado del expediente basándose en el estado del proceso
     *
     * Lógica:
     * - Si fecha_cierre está asignada: "Archivado" (archivado manualmente o proceso finalizado)
     * - Si no tiene seguimiento de proceso (tipo 3 Otros) y no está cerrado: "Abierto"
     * - Si no tiene estado de proceso asignado (LSO sin iniciar): "Abierto"
     * - Si el estado del proceso es final: "Archivado"
     * - Si tipo_accion es 'gestor' o 'decision': "Pendiente de acción"
     * - Si tipo_accion es 'espera_juzgado' o 'espera_tiempo': "Pendiente de notificación"
     */
    public function sincronizarEstado(): void
    {
        if ($this->fecha_cierre) {
            $estadoNombre = 'Archivado';
        } elseif (!$this->tieneSeguimientoProceso() || !$this->estadoProceso) {
            $estadoNombre = 'Abierto';
        } elseif ($this->estadoProceso->es_final) {
            $estadoNombre = 'Archivado';
        } else {
            $estadoNombre = match ($this->estadoProceso->tipo_accion) {
                'gestor', 'decision' => 'Pendiente de acción',
                'espera_juzgado', 'espera_tiempo' => 'Pendiente de notificación',
                default => 'Pendiente de acción',
            };
        }

        $estado = EstadoExpediente::where('estado', $estadoNombre)->first();
        if ($estado && $this->estado_id !== $estado->id) {
            $this->estado_id = $estado->id;
            $this->save();
        }
    }

    /**
     * Obtener los datos de publicaciones (BOE/RPC)
     * Aplica a LSO sin masa (1) y LSO con plan (2)
     */
    public function getDatosPublicaciones(): array
    {
        return [
            'fecha_publicacion_boe' => $this->fecha_publicacion_boe,
            'fecha_publicacion_rpc' => $this->fecha_publicacion_rpc,
        ];
    }

    /**
     * Registrar una fecha de publicacion (BOE o RPC)
     */
    public function registrarPublicacion(string $tipo, $fecha): bool
    {
        if (!in_array($tipo, ['boe', 'rpc'])) {
            return false;
        }

        if ($this->fecha_cierre) {
            return false; // Expediente archivado
        }

        // Solo aplica a tipos con seguimiento de proceso (1, 2)
        if (!$this->tieneSeguimientoProceso()) {
            return false;
        }

        $campo = 'fecha_publicacion_' . $tipo;
        $this->{$campo} = $fecha;

        return $this->save();
    }
}
