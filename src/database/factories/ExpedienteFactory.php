<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\EstadoExpediente;
use App\Models\Expediente;
use App\Models\TipoProcedimiento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expediente>
 */
class ExpedienteFactory extends Factory
{
    public function definition(): array
    {
        $fechaApertura = fake()->dateTimeBetween('-2 years', 'now');
        $archivado = fake()->boolean(20);
        $fechaCierre = $archivado ? fake()->dateTimeBetween($fechaApertura, 'now') : null;
        $estadoNombre = $archivado ? 'Archivado' : 'Abierto';

        return [
            'numero_expediente' => 'EXP-TMP-' . Str::random(12),
            'cliente_id' => Cliente::factory(),
            'gestor_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'tipo_procedimiento_id' => TipoProcedimiento::inRandomOrder()->first()?->id ?? 1,
            'fecha_apertura' => $fechaApertura,
            'fecha_cierre' => $fechaCierre,
            'estado_id' => EstadoExpediente::where('estado', $estadoNombre)->first()?->id ?? 1,
            'created_at' => $fechaApertura,
            'updated_at' => $fechaCierre ?? $fechaApertura,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Expediente $expediente) {
            $expediente->numero_expediente = 'EXP-' . str_pad((string) $expediente->id, 6, '0', STR_PAD_LEFT);
            $expediente->saveQuietly();
        });
    }

    /**
     * Expediente archivado (con fecha_cierre).
     */
    public function archivado(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'fecha_cierre' => fake()->dateTimeBetween($attributes['fecha_apertura'], 'now'),
                'estado_id' => EstadoExpediente::where('estado', 'Archivado')->first()?->id ?? 1,
            ];
        });
    }
}
