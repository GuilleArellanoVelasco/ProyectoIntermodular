<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documentos y tablas pivote
     */
    public function up(): void
    {
        // Documentos
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('nombre');
            $table->string('ruta');
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedBigInteger('tamanio')->default(0);
            $table->timestamps();
        });

        // Tabla pivote: documento_cliente (muchos a muchos)
        Schema::create('documento_cliente', function (Blueprint $table) {
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['documento_id', 'cliente_id']);
        });

        // Tabla pivote: documento_expediente (muchos a muchos)
        Schema::create('documento_expediente', function (Blueprint $table) {
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['documento_id', 'expediente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_expediente');
        Schema::dropIfExists('documento_cliente');
        Schema::dropIfExists('documentos');
    }
};
