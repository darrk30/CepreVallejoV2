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
        Schema::create('respuestas_correctas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_ordinario_id')->constrained('examen_ordinarios')->cascadeOnDelete();
            $table->unsignedInteger('numero_pregunta');                    // Ej: 1, 2, 3... 100
            $table->char('opcion_correcta', 1);                           // A, B, C, D, E
            $table->unsignedTinyInteger('bloque');                        // 1 = Habilidades, 2 = Especialidad
            $table->string('asignatura');                                  // verbal, matematica, fisica, etc.
            $table->timestamps();
            $table->unique(['examen_ordinario_id', 'numero_pregunta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_correctas');
    }
};
