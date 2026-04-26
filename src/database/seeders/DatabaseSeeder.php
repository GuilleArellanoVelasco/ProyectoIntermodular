<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Primero las tablas catálogo (sin dependencias)
            CatalogSeeder::class,

            // 2. Autómatas de estados para procesos (depende de tipos_procedimiento)
            ProcesoEstadosSeeder::class,

            // 3. Roles y permisos
            RoleSeeder::class,

            // 4. Datos de prueba (usuarios, clientes, expedientes, etc.)
            TestDataSeeder::class,
        ]);
    }
}
