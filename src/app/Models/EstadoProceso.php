<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProceso extends Model
{
    use HasFactory;

    protected $table = 'estados_proceso';

    protected $fillable = [
        'tipo_procedimiento_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo_accion',
        'es_inicial',
        'es_final',
        'resultado_final',
        'orden',
        'color',
        'icono',
    ];

    protected $casts = [
        'es_inicial' => 'boolean',
        'es_final' => 'boolean',
    ];

    public function tipoProcedimiento()
    {
        return $this->belongsTo(TipoProcedimiento::class, 'tipo_procedimiento_id');
    }

    public function transicionesSalientes()
    {
        return $this->hasMany(TransicionProceso::class, 'estado_origen_id');
    }

    public function transicionesEntrantes()
    {
        return $this->hasMany(TransicionProceso::class, 'estado_destino_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialProcesoExpediente::class, 'estado_id');
    }

    /**
     * Obtener los estados destino posibles desde este estado
     */
    public function estadosDestino()
    {
        return $this->belongsToMany(
            EstadoProceso::class,
            'transiciones_proceso',
            'estado_origen_id',
            'estado_destino_id'
        )->withPivot(['etiqueta', 'descripcion', 'requiere_confirmacion', 'es_principal']);
    }

    /**
     * Verificar si puede transicionar a un estado específico
     */
    public function puedeTransicionarA(EstadoProceso $estadoDestino): bool
    {
        return $this->transicionesSalientes()
            ->where('estado_destino_id', $estadoDestino->id)
            ->exists();
    }

    /**
     * Obtener la transición hacia un estado destino
     */
    public function getTransicionHacia(EstadoProceso $estadoDestino): ?TransicionProceso
    {
        return $this->transicionesSalientes()
            ->where('estado_destino_id', $estadoDestino->id)
            ->first();
    }

    /**
     * Scope para estados iniciales
     */
    public function scopeInicial($query)
    {
        return $query->where('es_inicial', true);
    }

    /**
     * Scope para estados finales
     */
    public function scopeFinal($query)
    {
        return $query->where('es_final', true);
    }

    /**
     * Scope por tipo de procedimiento
     */
    public function scopePorTipoProcedimiento($query, int $tipoProcedimientoId)
    {
        return $query->where('tipo_procedimiento_id', $tipoProcedimientoId);
    }

    /**
     * Obtener el color CSS para el badge
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->tipo_accion) {
            'gestor' => 'badge-primary',
            'espera_juzgado' => 'badge-proceso',
            'espera_tiempo' => 'badge-pendiente',
            'decision' => 'badge-revision',
            default => 'badge-proceso',
        };
    }
}