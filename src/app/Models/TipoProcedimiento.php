<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProcedimiento extends Model
{
    protected $table = 'tipos_procedimiento';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'tipo_procedimiento_id');
    }
}
