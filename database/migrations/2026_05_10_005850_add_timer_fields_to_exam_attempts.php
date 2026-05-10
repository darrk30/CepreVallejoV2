<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_attempts', 'segundos_restantes')) {
                $table->unsignedInteger('segundos_restantes')->default(0)->after('estado');
            }
            // timer_synced_at: momento exacto en que el JS envió el tiempo
            // Permite calcular elapsed = now() - timer_synced_at al retomar
            // Es distinto a updated_at (que cambia con cualquier operación)
            if (!Schema::hasColumn('exam_attempts', 'timer_synced_at')) {
                $table->timestamp('timer_synced_at')->nullable()->after('segundos_restantes');
            }
            // Eliminar columna vieja si existe
            if (Schema::hasColumn('exam_attempts', 'timer_updated_at')) {
                $table->dropColumn('timer_updated_at');
            }
            if (Schema::hasColumn('exam_attempts', 'duracion_minutos_restantes')) {
                $table->dropColumn('duracion_minutos_restantes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumnIfExists('segundos_restantes');
            $table->dropColumnIfExists('timer_synced_at');
        });
    }
};