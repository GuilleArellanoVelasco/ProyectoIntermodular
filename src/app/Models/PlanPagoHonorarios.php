<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPagoHonorarios extends Model
{
    protected $table = 'planes_pago_honorarios';

    protected $fillable = [
        'expediente_id',
        'importe_total',
        'numero_cuotas',
        'importe_cuota',
        'fecha_primer_vencimiento',
    ];

    protected $casts = [
        'importe_total' => 'decimal:2',
        'importe_cuota' => 'decimal:2',
        'numero_cuotas' => 'integer',
        'fecha_primer_vencimiento' => 'date',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function cuotas()
    {
        return $this->hasMany(CuotaHonorarios::class, 'plan_pago_honorarios_id')->orderBy('numero_cuota');
    }
}
