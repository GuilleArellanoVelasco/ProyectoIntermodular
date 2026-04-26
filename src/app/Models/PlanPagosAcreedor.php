<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPagosAcreedor extends Model
{
    protected $table = 'plan_pagos_acreedores';

    protected $fillable = [
        'expediente_id',
        'nombre_acreedor',
        'deuda_original',
        'propuesta',
        'pagado',
        'fecha_pago',
    ];

    protected $casts = [
        'deuda_original' => 'decimal:2',
        'propuesta' => 'decimal:2',
        'pagado' => 'boolean',
        'fecha_pago' => 'date',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
