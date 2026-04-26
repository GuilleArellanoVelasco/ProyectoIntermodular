<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'tipo_documento_id',
        'uploaded_by',
        'nombre',
        'ruta',
        'mime_type',
        'tamanio',
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'documento_cliente');
    }

    public function expedientes()
    {
        return $this->belongsToMany(Expediente::class, 'documento_expediente');
    }

    // Accessor para tamaño formateado
    public function getTamanioFormateadoAttribute(): string
    {
        $bytes = $this->tamanio;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
