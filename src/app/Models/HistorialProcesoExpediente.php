<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialProcesoExpediente extends Model
{
    use HasFactory;

    protected $table = 'historial_proceso_expediente';

    public $timestamps = false;

    protected $fillable = [
        'expediente_id',
        'estado_id',
        'transicion_id',
        'usuario_id',
        'fecha_entrada',
        'fecha_salida',
    ];

    protected $casts = [
        'fecha_entrada' => 'datetime',
        'fecha_salida' => 'datetime',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function estado()
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_id');
    }

    public function transicion()
    {
        return $this->belongsTo(TransicionProceso::class, 'transicion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para el estado actual (sin fecha de salida)
     */
    public function scopeActual($query)
    {
        return $query->whereNull('fecha_salida');
    }

    /**
     * Calcular duración en este estado
     */
    public function getDuracionAttribute(): ?int
    {
        if (!$this->fecha_entrada) {
            return null;
        }

        $fin = $this->fecha_salida ?? now();
        return $this->fecha_entrada->diffInDays($fin);
    }
}