<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas catálogo/lookup - Sin dependencias FK
     */
    public function up(): void
    {
        // Tipos de documentación (DNI, NIE, Pasaporte)
        Schema::create('tipos_documentacion', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento')->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        // Estados de expediente
        Schema::create('estados_expediente', function (Blueprint $table) {
            $table->id();
            $table->string('estado')->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        // Tipos de procedimiento
        Schema::create('tipos_procedimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Tipos de documento
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento');
        Schema::dropIfExists('tipos_procedimiento');
        Schema::dropIfExists('estados_expediente');
        Schema::dropIfExists('tipos_documentacion');
    }
};
