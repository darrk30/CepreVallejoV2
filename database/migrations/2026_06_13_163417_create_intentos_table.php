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
        Schema::create('intentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('examen_ordinario_id')->constrained('examen_ordinarios')->cascadeOnDelete();

            $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();

            $table->decimal('puntaje_obtenido', 8, 2)->nullable();
            $table->boolean('es_aprobado')->nullable();

            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->unsignedInteger('tiempo_utilizado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intentos');
    }
};
