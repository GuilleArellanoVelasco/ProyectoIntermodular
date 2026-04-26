<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuotas_honorarios', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'domiciliacion'])->nullable()->after('fecha_pago');
        });

        Schema::create('facturas_honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuota_honorarios_id')->unique()->constrained('cuotas_honorarios')->onDelete('cascade');
            $table->string('numero_factura')->unique();
            $table->date('fecha_emision');
            $table->decimal('importe', 10, 2);
            $table->string('documento_pdf_ruta')->nullable();
            $table->string('documento_pdf_nombre')->nullable();
            $table->timestamp('documento_pdf_subido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_honorarios');

        Schema::table('cuotas_honorarios', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};
