<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransicionProceso extends Model
{
    use HasFactory;

    protected $table = 'transiciones_proceso';

    protected $fillable = [
        'estado_origen_id',
        'estado_destino_id',
        'etiqueta',
        'descripcion',
        'requiere_confirmacion',
        'es_principal',
    ];

    protected $casts = [
        'requiere_confirmacion' => 'boolean',
        'es_principal' => 'boolean',
    ];

    public function estadoOrigen()
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_origen_id');
    }

    public function estadoDestino()
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_destino_id');
    }

    /**
     * Scope para transiciones principales
     */
    public function scopePrincipal($query)
    {
        return $query->where('es_principal', true);
    }
}