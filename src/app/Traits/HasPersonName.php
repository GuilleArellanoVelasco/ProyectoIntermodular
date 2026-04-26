<?php

namespace App\Traits;

/**
 * Trait para modelos que representan personas con nombre y apellidos.
 * Proporciona accessors comunes: nombreCompleto, iniciales, avatarColor
 */
trait HasPersonName
{
    /**
     * Obtener el nombre completo de la persona
     */
    public function getNombreCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->nombre ?? null,
            $this->apellido1 ?? null,
            $this->apellido2 ?? null,
        ]);

        return trim(implode(' ', $partes));
    }

    /**
     * Obtener las iniciales para mostrar en avatares
     */
    public function getInicialesAttribute(): string
    {
        $iniciales = '';

        if (!empty($this->nombre)) {
            $iniciales .= mb_substr($this->nombre, 0, 1);
        }

        if (!empty($this->apellido1)) {
            $iniciales .= mb_substr($this->apellido1, 0, 1);
        }

        return mb_strtoupper($iniciales);
    }

    /**
     * Obtener el color de avatar basado en el ID
     * Devuelve una clase CSS de Tailwind
     */
    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-primary-400',
            'bg-accent-blue',
            'bg-accent-purple',
            'bg-success',
            'bg-accent-pink',
            'bg-warning',
            'bg-info',
        ];

        $id = $this->id ?? 0;
        return $colors[$id % count($colors)];
    }
}
