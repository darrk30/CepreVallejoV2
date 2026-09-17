<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tareas_programadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Comando artisan a ejecutar. Por seguridad, routes/console.php solo
            // registra los que están en TareaProgramada::comandosDisponibles();
            // esto es una capa extra, no la única.
            $table->string('comando');

            // Frecuencia (App\Enums\FrecuenciaTarea) + parámetros según cuál sea
            $table->string('frecuencia')->default('diaria');
            $table->time('hora')->nullable();               // diaria / semanal / mensual
            $table->unsignedTinyInteger('dia_semana')->nullable(); // 0=domingo .. 6=sábado
            $table->unsignedTinyInteger('dia_mes')->nullable();    // 1..31
            $table->string('expresion_cron')->nullable();    // solo si frecuencia = personalizada

            $table->boolean('activo')->default(true);

            // Resultado de la última ejecución (programada o manual)
            $table->timestamp('ultima_ejecucion_at')->nullable();
            $table->string('ultimo_resultado')->nullable(); // App\Enums\ResultadoTarea
            $table->longText('ultima_salida')->nullable();

            $table->timestamps();
        });

        // Damos de alta la tarea que hoy vivía hardcodeada en routes/console.php,
        // para que el comportamiento no cambie al pasar a este sistema.
        DB::table('tareas_programadas')->insert([
            'nombre' => 'Cerrar matrículas vencidas',
            'descripcion' => 'Cuando un ciclo académico llega a su fecha fin: inactiva sus matrículas y desactiva el ciclo.',
            'comando' => 'matriculas:cerrar-vencidas',
            'frecuencia' => 'diaria',
            'hora' => '00:05:00',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_programadas');
    }
};
