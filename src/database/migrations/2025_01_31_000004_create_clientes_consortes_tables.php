<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clientes y Consortes
     */
    public function up(): void
    {
        // Clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('set null');
            $table->string('nombre');
            $table->string('apellido1');
            $table->string('apellido2')->nullable();
            $table->foreignId('tipo_documentacion_id')->constrained('tipos_documentacion');
            $table->string('numero_documentacion')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Consortes (cónyuges asociados a clientes)
        Schema::create('consortes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellido1');
            $table->string('apellido2')->nullable();
            $table->foreignId('tipo_documentacion_id')->constrained('tipos_documentacion');
            $table->string('numero_documentacion')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consortes');
        Schema::dropIfExists('clientes');
    }
};
