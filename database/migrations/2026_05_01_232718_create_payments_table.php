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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');

            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago')->nullable(); // Efectivo, Yape, Transferencia (null si es pendiente)
            $table->date('fecha_vencimiento')->nullable(); // Para pagos programados
            $table->dateTime('fecha_pago')->nullable(); // Se llena cuando se marca como 'pagado'
            $table->string('referencia')->nullable(); // N° de operación
            $table->string('estado')->default('pendiente'); // 'pendiente', 'pagado'

            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
