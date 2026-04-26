<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Eventos del calendario: recordatorios personales y alertas generadas por estados de expediente
     */
    public function up(): void
    {
        Schema::create('eventos_calendario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['recordatorio', 'alerta']);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->foreignId('expediente_id')->nullable()->constrained('expedientes')->onDelete('cascade');
            $table->foreignId('estado_proceso_id')->nullable()->constrained('estados_proceso')->nullOnDelete();
            $table->timestamp('resuelto_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'fecha']);
            $table->index(['expediente_id', 'estado_proceso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_calendario');
    }
};
