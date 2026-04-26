<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentacion extends Model
{
    protected $table = 'tipos_documentacion';
    public $timestamps = false;

    protected $fillable = ['tipo_documento'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'tipo_documentacion_id');
    }

    public function consortes()
    {
        return $this->hasMany(Consorte::class, 'tipo_documentacion_id');
    }
}
