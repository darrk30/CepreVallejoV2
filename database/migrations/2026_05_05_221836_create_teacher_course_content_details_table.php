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
        Schema::create('teacher_course_content_details', function (Blueprint $table) {
            $table->id();
            // Relación con el padre (Semana/Sección)
            $table->foreignId('teacher_course_content_id')->constrained('teacher_course_contents')->onDelete('cascade');

            // Información del Tema
            $table->string('titulo'); // Ej: "Tema 1: Triángulos"
            $table->text('descripcion')->nullable();
            
            // Recursos
            $table->string('archivo_path')->nullable();
            $table->string('url_video')->nullable();
            
            // Control y Orden
            $table->integer('orden')->default(1);
            $table->string('estado')->default('activo');

            // Auditoría
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_course_content_details');
    }
};
