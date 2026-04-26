<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'cif' => fake()->unique()->regexify('[A-Z][0-9]{8}'),
            'email' => fake()->companyEmail(),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
        ];
    }
}
