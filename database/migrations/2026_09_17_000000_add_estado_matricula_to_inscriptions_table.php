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
        Schema::table('inscriptions', function (Blueprint $table) {
            // Estado de la matrícula en sí (activa/inactiva), independiente
            // del estado de pago (estado_pago: pendiente/parcial/pagado).
            // Al agregar la columna con DEFAULT, MySQL backfillea las filas
            // existentes con 'activa'.
            $table->string('estado_matricula')->default('activa')->after('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn('estado_matricula');
        });
    }
};
