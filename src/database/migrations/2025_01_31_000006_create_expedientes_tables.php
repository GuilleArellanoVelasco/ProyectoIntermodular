<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expedientes y tablas relacionadas
     */
    public function up(): void
    {
        // Expedientes
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_expediente')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('gestor_id')->constrained('users');
            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimiento');
            $table->date('fecha_apertura');
            $table->date('fecha_cierre')->nullable();
            $table->foreignId('estado_id')->constrained('estados_expediente');
            // Fechas de publicación (comunes a LSO sin masa y LSO con plan)
            $table->date('fecha_publicacion_boe')->nullable();
            $table->date('fecha_publicacion_rpc')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Otros procedimientos
        Schema::create('otros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->date('fecha_actuacion')->nullable();
            $table->string('tipo_encargo')->nullable();
            $table->timestamps();
        });

        // Plan de pagos a acreedores
        // Cada fila es una línea del plan: un acreedor con su deuda y propuesta.
        // El nombre del acreedor se escribe a mano (no hay tabla de acreedores).
        Schema::create('plan_pagos_acreedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->string('nombre_acreedor');
            $table->decimal('deuda_original', 10, 2);
            $table->decimal('propuesta', 10, 2);
            $table->boolean('pagado')->default(false);
            $table->date('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_pagos_acreedores');
        Schema::dropIfExists('otros');
        Schema::dropIfExists('expedientes');
    }
};
