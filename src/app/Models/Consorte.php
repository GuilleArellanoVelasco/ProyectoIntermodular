<?php

namespace App\Models;

use App\Traits\HasPersonName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consorte extends Model
{
    use HasFactory, SoftDeletes, HasPersonName;

    protected $fillable = [
        'cliente_id',
        'nombre',
        'apellido1',
        'apellido2',
        'tipo_documentacion_id',
        'numero_documentacion',
        'email',
        'telefono',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipoDocumentacion()
    {
        return $this->belongsTo(TipoDocumentacion::class, 'tipo_documentacion_id');
    }
}
