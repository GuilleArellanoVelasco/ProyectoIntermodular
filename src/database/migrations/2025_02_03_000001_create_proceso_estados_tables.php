<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de autómata de estados para el progreso de expedientes
     */
    public function up(): void
    {
        // Estados del proceso (nodos del autómata)
        Schema::create('estados_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_procedimiento_id')->constrained('tipos_procedimiento');
            $table->string('codigo', 50); // Identificador único: 'recopilar_docs'
            $table->string('nombre', 100); // Nombre legible: 'Recopilar Documentación'
            $table->text('descripcion')->nullable();
            $table->enum('tipo_accion', ['gestor', 'espera_juzgado', 'espera_tiempo', 'decision']);
            $table->boolean('es_inicial')->default(false);
            $table->boolean('es_final')->default(false);
            $table->enum('resultado_final', ['exito', 'fracaso'])->nullable(); // Solo para estados finales
            $table->unsignedTinyInteger('orden')->default(0); // Para ordenar en visualización
            $table->string('color', 20)->nullable(); // Color para UI
            $table->string('icono', 50)->nullable(); // Icono para UI
            $table->timestamps();

            $table->unique(['tipo_procedimiento_id', 'codigo']);
        });

        // Transiciones válidas (flechas del autómata)
        Schema::create('transiciones_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estado_origen_id')->constrained('estados_proceso')->onDelete('cascade');
            $table->foreignId('estado_destino_id')->constrained('estados_proceso')->onDelete('cascade');
            $table->string('etiqueta', 100); // 'Admitido', 'Inadmitido', 'Continuar'
            $table->text('descripcion')->nullable();
            $table->boolean('requiere_confirmacion')->default(false);
            $table->boolean('es_principal')->default(true); // Transición principal vs alternativa
            $table->timestamps();

            $table->unique(['estado_origen_id', 'estado_destino_id']);
        });

        // Historial del proceso de cada expediente
        Schema::create('historial_proceso_expediente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('estados_proceso');
            $table->foreignId('transicion_id')->nullable()->constrained('transiciones_proceso');
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamp('fecha_entrada');
            $table->timestamp('fecha_salida')->nullable(); // NULL = estado actual
            $table->timestamp('created_at')->useCurrent();
        });

        // Añadir estado actual del proceso al expediente
        Schema::table('expedientes', function (Blueprint $table) {
            $table->foreignId('estado_proceso_id')->nullable()->after('estado_id')
                ->constrained('estados_proceso')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropForeign(['estado_proceso_id']);
            $table->dropColumn('estado_proceso_id');
        });

        Schema::dropIfExists('historial_proceso_expediente');
        Schema::dropIfExists('transiciones_proceso');
        Schema::dropIfExists('estados_proceso');
    }
};