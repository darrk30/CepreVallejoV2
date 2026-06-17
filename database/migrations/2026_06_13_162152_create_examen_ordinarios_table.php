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
        Schema::create('examen_ordinarios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('pdf_path')->comment('Ruta donde se guarda el archivo PDF del examen');
            $table->unsignedInteger('duracion_minutos')->comment('Tiempo máximo en minutos para resolver el examen');
            $table->string('estado')->default('activo');
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_ordinarios');
    }
};
