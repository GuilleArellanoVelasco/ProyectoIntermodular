<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\TipoDocumentacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consorte>
 */
class ConsorteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'nombre' => fake()->firstName(),
            'apellido1' => fake()->lastName(),
            'apellido2' => fake()->lastName(),
            'tipo_documentacion_id' => TipoDocumentacion::inRandomOrder()->first()?->id ?? 1,
            'numero_documentacion' => fake()->unique()->regexify('[0-9]{8}[A-Z]'),
            'email' => fake()->optional(0.8)->safeEmail(),
            'telefono' => fake()->optional(0.8)->phoneNumber(),
        ];
    }
}
