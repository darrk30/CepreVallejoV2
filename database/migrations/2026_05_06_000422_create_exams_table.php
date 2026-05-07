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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            // Relación con el contenido del profesor
            // $table->foreignId('teacher_course_content_id')->constrained('teacher_course_contents')->cascadeOnDelete();
            $table->foreignId('teacher_course_content_detail_id')->constrained('teacher_course_content_details')->onDelete('cascade');

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            
            // Configuración del test
            $table->integer('duracion_minutos')->default(60);
            $table->integer('intentos_maximos')->default(1);
            $table->decimal('puntaje_minimo', 5, 2)->default(10.50); // Para aprobar
            
            $table->string('estado')->default('activo'); // activo, inactivo, borrador
            
            // Auditoría (consistente con tus otros modelos)
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
