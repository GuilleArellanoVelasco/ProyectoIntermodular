<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notificacion>
 */
class NotificacionFactory extends Factory
{
    public function definition(): array
    {
        $leida = fake()->boolean(40);

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'tipo' => fake()->randomElement(['info', 'warning', 'error', 'success', 'tarea', 'expediente']),
            'titulo' => fake()->sentence(4),
            'mensaje' => fake()->optional(0.8)->paragraph(),
            'leida' => $leida,
            'leida_at' => $leida ? fake()->dateTimeBetween('-1 week', 'now') : null,
        ];
    }

    /**
     * Notificación no leída
     */
    public function noLeida(): static
    {
        return $this->state(fn (array $attributes) => [
            'leida' => false,
            'leida_at' => null,
        ]);
    }
}
