<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Consorte;
use App\Models\Empresa;
use App\Models\Expediente;
use App\Models\Notificacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'nombre' => 'Admin',
            'apellido1' => 'Sistema',
            'apellido2' => 'Liberxo',
            'email' => 'admin@liberxo.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Asignar rol de admin
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role_id' => DB::table('roles')->where('name', 'admin')->first()->id,
        ]);

        // Crear gestores
        $gestores = User::factory(5)->create();
        $rolGestor = DB::table('roles')->where('name', 'gestor')->first()->id;
        foreach ($gestores as $gestor) {
            DB::table('role_user')->insert([
                'user_id' => $gestor->id,
                'role_id' => $rolGestor,
            ]);
        }

        // Crear empresas
        $empresas = Empresa::factory(5)->create();

        // Crear clientes (algunos con empresa, algunos con consorte)
        $clientes = Cliente::factory(30)->create();

        // Crear consortes para algunos clientes
        $clientesConConsorte = $clientes->random(10);
        foreach ($clientesConConsorte as $cliente) {
            Consorte::factory()->create(['cliente_id' => $cliente->id]);
        }

        // Crear expedientes
        $expedientes = [];
        foreach ($clientes->random(20) as $cliente) {
            $expediente = Expediente::factory()->create([
                'cliente_id' => $cliente->id,
                'gestor_id' => $gestores->random()->id,
            ]);

            $expedientes[] = $expediente;
        }

        // Crear tareas para los expedientes
        foreach ($expedientes as $expediente) {
            $numTareas = rand(1, 5);
            for ($i = 0; $i < $numTareas; $i++) {
                Tarea::factory()->create([
                    'expediente_id' => $expediente->id,
                    'asignado_a' => collect([$admin, ...$gestores])->random()->id,
                    'creado_por' => collect([$admin, ...$gestores])->random()->id,
                ]);
            }
        }

        // Crear notificaciones para todos los usuarios
        $todosUsuarios = User::all();
        foreach ($todosUsuarios as $usuario) {
            Notificacion::factory(rand(3, 10))->create([
                'user_id' => $usuario->id,
            ]);
        }

        $this->command->info('Datos de prueba creados:');
        $this->command->info('- 1 Administrador (admin@liberxo.com / password)');
        $this->command->info('- 5 Gestores');
        $this->command->info('- 5 Empresas');
        $this->command->info('- 30 Clientes');
        $this->command->info('- ~20 Expedientes');
        $this->command->info('- Múltiples tareas y notificaciones');
    }
}
