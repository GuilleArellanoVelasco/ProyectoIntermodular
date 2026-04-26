<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    /**
     * Seed de todas las tablas catálogo/lookup
     */
    public function run(): void
    {
        // Tipos de documentación
        DB::table('tipos_documentacion')->insert([
            ['tipo_documento' => 'DNI'],
            ['tipo_documento' => 'NIE'],
            ['tipo_documento' => 'Pasaporte'],
        ]);

        // Estados de expediente
        // - Pendiente de acción: cuando el gestor debe realizar alguna acción
        // - Pendiente de notificación: cuando se espera respuesta de terceros (juzgado, etc.)
        // - Archivado: expedientes finalizados
        // - Abierto: para expedientes sin proceso definido (tipo "Otros")
        DB::table('estados_expediente')->insert([
            ['estado' => 'Pendiente de acción'],
            ['estado' => 'Pendiente de notificación'],
            ['estado' => 'Archivado'],
            ['estado' => 'Abierto'],
        ]);

        // Tipos de procedimiento
        DB::table('tipos_procedimiento')->insert([
            ['nombre' => 'LSO sin masa', 'descripcion' => 'Ley de Segunda Oportunidad sin masa activa'],
            ['nombre' => 'LSO con plan de pagos', 'descripcion' => 'Ley de Segunda Oportunidad con plan de pagos'],
            ['nombre' => 'Otros', 'descripcion' => 'Otros procedimientos'],
        ]);

        // Tipos de documento
        DB::table('tipos_documento')->insert([
            ['nombre' => 'DNI/NIE', 'descripcion' => 'Documento de identidad'],
            ['nombre' => 'Contrato', 'descripcion' => 'Contrato firmado'],
            ['nombre' => 'Factura', 'descripcion' => 'Factura emitida o recibida'],
            ['nombre' => 'Escritura', 'descripcion' => 'Escritura notarial'],
            ['nombre' => 'Sentencia', 'descripcion' => 'Sentencia judicial'],
            ['nombre' => 'Auto', 'descripcion' => 'Auto judicial'],
            ['nombre' => 'Demanda', 'descripcion' => 'Escrito de demanda'],
            ['nombre' => 'Contestación', 'descripcion' => 'Escrito de contestación'],
            ['nombre' => 'Recurso', 'descripcion' => 'Escrito de recurso'],
            ['nombre' => 'Notificación', 'descripcion' => 'Notificación oficial'],
            ['nombre' => 'Informe', 'descripcion' => 'Informe o dictamen'],
            ['nombre' => 'Certificado', 'descripcion' => 'Certificado oficial'],
            ['nombre' => 'Extracto bancario', 'descripcion' => 'Extracto de cuenta bancaria'],
            ['nombre' => 'Nómina', 'descripcion' => 'Nómina o recibo de salario'],
            ['nombre' => 'Declaración IRPF', 'descripcion' => 'Declaración de la renta'],
            ['nombre' => 'Vida laboral', 'descripcion' => 'Informe de vida laboral'],
            ['nombre' => 'Otro', 'descripcion' => 'Otro tipo de documento'],
        ]);
    }
}
