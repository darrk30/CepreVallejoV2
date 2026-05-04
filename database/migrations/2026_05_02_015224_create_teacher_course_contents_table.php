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
        Schema::create('teacher_course_contents', function (Blueprint $table) {
            $table->id();

            // Relacionamos el contenido exactamente con "este profesor dictando este curso en este ciclo"
            $table->foreignId('ciclo_course_teacher_id')
                ->constrained('ciclo_course_teacher')
                ->cascadeOnDelete();

            $table->string('titulo'); // Ej: "Semana 1: Ecuaciones de primer grado"
            $table->text('descripcion')->nullable(); // Detalles de la tarea o clase

            // Recursos multimedia (Diseño pensando en mobile-first para que el alumno lo descargue fácil)
            $table->string('archivo_path')->nullable(); // Para PDFs, sílabos o diapositivas
            $table->string('url_video')->nullable(); // Link a Zoom, YouTube o Drive

            $table->string('estado')->default('Activo');
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_course_contents');
    }
};
