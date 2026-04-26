<?php

namespace App\Models;

use App\Traits\HasPersonName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPersonName;

    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function expedientesComoGestor()
    {
        return $this->hasMany(Expediente::class, 'gestor_id');
    }

    public function tareasAsignadas()
    {
        return $this->hasMany(Tarea::class, 'asignado_a');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'creado_por');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function eventosCalendario()
    {
        return $this->hasMany(EventoCalendario::class);
    }

    public function documentosSubidos()
    {
        return $this->hasMany(Documento::class, 'uploaded_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function updateLastLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }
}
