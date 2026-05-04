<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_payments', function (Blueprint $table) {
            $table->id();

            // ¿A quién se le paga?
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();

            // ¿Por qué ciclo se le está pagando? (Opcional, pero recomendado para reportes)
            $table->foreignId('academic_cycle_id')->nullable()->constrained()->nullOnDelete();

            // Detalles financieros
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago'); // Ej: Yape, Plin, BCP, Efectivo
            $table->string('numero_operacion')->nullable(); // Voucher

            // Archivo de respaldo (Recibo por Honorarios, Factura, o foto del voucher)
            $table->string('comprobante_path')->nullable();

            $table->string('estado')->default('Completado'); // Pendiente, Completado, Anulado
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_payments');
    }
};
