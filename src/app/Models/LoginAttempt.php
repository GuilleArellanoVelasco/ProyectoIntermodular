<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    // Desactivamos timestamps automáticos porque usamos 'attempted_at'
    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'user_agent',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    /**
     * Registrar un intento de login
     */
    public static function log(string $email, string $ipAddress, bool $successful, ?string $userAgent = null): void
    {
        self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'successful' => $successful,
            'user_agent' => $userAgent,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Obtener intentos fallidos recientes de un email
     */
    public static function recentFailedAttempts(string $email, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Obtener intentos fallidos recientes de una IP
     */
    public static function recentFailedAttemptsByIp(string $ipAddress, int $minutes = 15): int
    {
        return self::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Limpiar intentos antiguos (ejecutar en cron job)
     */
    public static function cleanOldAttempts(int $days = 30): int
    {
        return self::where('attempted_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Scope para intentos exitosos
     */
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    /**
     * Scope para intentos fallidos
     */
    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    /**
     * Scope para intentos recientes
     */
    public function scopeRecent($query, int $minutes = 15)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }
}
