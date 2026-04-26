<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaHonorarios extends Model
{
    protected $table = 'facturas_honorarios';

    protected $fillable = [
        'cuota_honorarios_id',
        'numero_factura',
        'fecha_emision',
        'importe',
        'documento_pdf_ruta',
        'documento_pdf_nombre',
        'documento_pdf_subido_at',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'importe' => 'decimal:2',
        'documento_pdf_subido_at' => 'datetime',
    ];

    public function cuota()
    {
        return $this->belongsTo(CuotaHonorarios::class, 'cuota_honorarios_id');
    }

    public function getTienePdfAttribute(): bool
    {
        return !empty($this->documento_pdf_ruta);
    }
}
