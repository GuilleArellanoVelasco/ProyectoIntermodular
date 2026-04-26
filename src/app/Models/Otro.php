<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otro extends Model
{
    use HasFactory;

    protected $table = 'otros';

    protected $fillable = [
        'expediente_id',
        'fecha_actuacion',
        'tipo_encargo',
    ];

    protected $casts = [
        'fecha_actuacion' => 'date',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
