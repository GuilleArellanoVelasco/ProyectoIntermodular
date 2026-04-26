<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\TipoDocumentacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa_id' => fake()->optional(0.3)->randomElement(Empresa::pluck('id')->toArray() ?: [null]),
            'nombre' => fake()->firstName(),
            'apellido1' => fake()->lastName(),
            'apellido2' => fake()->lastName(),
            'tipo_documentacion_id' => TipoDocumentacion::inRandomOrder()->first()?->id ?? 1,
            'numero_documentacion' => fake()->unique()->regexify('[0-9]{8}[A-Z]'),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
        ];
    }

    /**
     * Cliente asociado a una empresa
     */
    public function conEmpresa(): static
    {
        return $this->state(fn (array $attributes) => [
            'empresa_id' => Empresa::factory(),
        ]);
    }
}
