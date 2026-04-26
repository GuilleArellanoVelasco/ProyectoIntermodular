<?php

namespace Database\Factories;

use App\Models\Expediente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tarea>
 */
class TareaFactory extends Factory
{
    public function definition(): array
    {
        $estado = fake()->randomElement(['pendiente', 'en_progreso', 'completada', 'cancelada']);

        return [
            'expediente_id' => Expediente::factory(),
            'asignado_a' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'creado_por' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'titulo' => fake()->sentence(4),
            'descripcion' => fake()->optional(0.7)->paragraph(),
            'fecha_vencimiento' => fake()->optional(0.8)->dateTimeBetween('now', '+3 months'),
            'prioridad' => fake()->randomElement(['baja', 'media', 'alta', 'urgente']),
            'estado' => $estado,
            'completada_at' => $estado === 'completada' ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    /**
     * Tarea urgente
     */
    public function urgente(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridad' => 'urgente',
            'fecha_vencimiento' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }

    /**
     * Tarea completada
     */
    public function completada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completada',
            'completada_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
