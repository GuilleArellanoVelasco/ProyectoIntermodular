<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoExpediente extends Model
{
    protected $table = 'estados_expediente';
    public $timestamps = false;

    protected $fillable = ['estado'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'estado_id');
    }

    /**
     * Obtener la clase CSS del badge según el estado
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'Pendiente de acción' => 'badge-accion',
            'Pendiente de notificación' => 'badge-notificacion',
            'Archivado' => 'badge-archivado',
            'Abierto' => 'badge-abierto',
            default => 'badge-proceso',
        };
    }
}
