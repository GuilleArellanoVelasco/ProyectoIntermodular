<?php

namespace App\Models;

use App\Traits\HasPersonName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, HasPersonName;

    protected $fillable = [
        'empresa_id',
        'nombre',
        'apellido1',
        'apellido2',
        'tipo_documentacion_id',
        'numero_documentacion',
        'email',
        'telefono',
        'direccion',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tipoDocumentacion()
    {
        return $this->belongsTo(TipoDocumentacion::class, 'tipo_documentacion_id');
    }

    public function consorte()
    {
        return $this->hasOne(Consorte::class);
    }

    public function expedientes()
    {
        return $this->hasMany(Expediente::class);
    }

    public function documentos()
    {
        return $this->belongsToMany(Documento::class, 'documento_cliente');
    }

    /**
     * Activo si tiene al menos un expediente no archivado.
     */
    public function getEstaActivoAttribute(): bool
    {
        return $this->expedientes()
            ->whereHas('estado', fn ($q) => $q->where('estado', '!=', 'Archivado'))
            ->exists();
    }

    public function getEstadoAttribute(): string
    {
        return $this->esta_activo ? 'Activo' : 'Inactivo';
    }

    public function getEstadoSlugAttribute(): string
    {
        return $this->esta_activo ? 'activo' : 'inactivo';
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->whereHas('expedientes.estado', fn ($q) => $q->where('estado', '!=', 'Archivado'));
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->whereDoesntHave('expedientes.estado', fn ($q) => $q->where('estado', '!=', 'Archivado'));
    }
}
