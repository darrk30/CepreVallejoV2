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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // El Alumno
            
            $table->decimal('puntaje_obtenido', 5, 2)->default(0.00);
            $table->integer('duracion_minutos_restantes')->nullable();
            // Guardamos las respuestas en formato JSON para auditoría rápida
            // Ejemplo: {"pregunta_1": "opcion_5", "pregunta_2": "opcion_2"}
            $table->json('respuestas_enviadas')->nullable();
            
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            
            $table->string('estado')->default('en_progreso'); // 'en_progreso', 'finalizado', 'expirado'
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
