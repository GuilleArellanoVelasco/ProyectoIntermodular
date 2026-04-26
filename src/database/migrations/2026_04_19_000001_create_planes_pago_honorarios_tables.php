<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_pago_honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->unique()->constrained('expedientes')->onDelete('cascade');
            $table->decimal('importe_total', 10, 2);
            $table->unsignedInteger('numero_cuotas');
            $table->decimal('importe_cuota', 10, 2);
            $table->date('fecha_primer_vencimiento');
            $table->timestamps();
        });

        Schema::create('cuotas_honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_pago_honorarios_id')->constrained('planes_pago_honorarios')->onDelete('cascade');
            $table->unsignedInteger('numero_cuota');
            $table->date('fecha_vencimiento');
            $table->decimal('importe', 10, 2);
            $table->boolean('pagada')->default(false);
            $table->date('fecha_pago')->nullable();
            $table->timestamps();

            $table->unique(['plan_pago_honorarios_id', 'numero_cuota']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas_honorarios');
        Schema::dropIfExists('planes_pago_honorarios');
    }
};
