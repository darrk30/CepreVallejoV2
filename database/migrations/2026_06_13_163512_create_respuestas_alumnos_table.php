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
        Schema::create('respuestas_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intento_id')->constrained('intentos')->cascadeOnDelete();
            $table->unsignedInteger('numero_pregunta');
            $table->decimal('puntos_obtenidos', 8, 4)->default(0);
            $table->char('opcion_seleccionada', 1)->nullable(); // Nullable por si deja la pregunta en blanco
            $table->timestamps();

            // Evita que un mismo intento tenga dos respuestas marcadas para la misma pregunta
            $table->unique(['intento_id', 'numero_pregunta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_alumnos');
    }
};
