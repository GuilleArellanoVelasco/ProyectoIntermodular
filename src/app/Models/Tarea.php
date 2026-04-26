<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_id',
        'asignado_a',
        'creado_por',
        'titulo',
        'descripcion',
        'fecha_vencimiento',
        'prioridad',
        'estado',
        'completada_at',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'completada_at' => 'datetime',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('fecha_vencimiento', '<', now())
                     ->whereNotIn('estado', ['completada', 'cancelada']);
    }
}
