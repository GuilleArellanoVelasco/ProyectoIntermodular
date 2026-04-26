<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuotaHonorarios extends Model
{
    protected $table = 'cuotas_honorarios';

    protected $fillable = [
        'plan_pago_honorarios_id',
        'numero_cuota',
        'fecha_vencimiento',
        'importe',
        'pagada',
        'fecha_pago',
        'metodo_pago',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'numero_cuota' => 'integer',
        'pagada' => 'boolean',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanPagoHonorarios::class, 'plan_pago_honorarios_id');
    }

    public function factura()
    {
        return $this->hasOne(FacturaHonorarios::class, 'cuota_honorarios_id');
    }
}
