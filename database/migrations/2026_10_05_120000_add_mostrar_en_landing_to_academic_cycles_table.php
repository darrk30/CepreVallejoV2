<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Visibilidad del ciclo SOLO en la landing. "estado" sigue controlando el
     * funcionamiento interno (matrículas, aula, cierre automático).
     */
    public function up(): void
    {
        Schema::table('academic_cycles', function (Blueprint $table) {
            $table->boolean('mostrar_en_landing')->default(true)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('academic_cycles', function (Blueprint $table) {
            $table->dropColumn('mostrar_en_landing');
        });
    }
};
