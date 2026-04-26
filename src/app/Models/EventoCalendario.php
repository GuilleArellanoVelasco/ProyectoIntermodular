<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoCalendario extends Model
{
    use HasFactory;

    protected $table = 'eventos_calendario';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'expediente_id',
        'estado_proceso_id',
        'resuelto_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'resuelto_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function estadoProceso()
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_proceso_id');
    }

    public function scopeActivos($query)
    {
        return $query->whereNull('resuelto_at');
    }

    public function scopeDelUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeEnFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeRecordatorios($query)
    {
        return $query->where('tipo', 'recordatorio');
    }

    public function scopeAlertas($query)
    {
        return $query->where('tipo', 'alerta');
    }
}
