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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->text('texto_pregunta');
            $table->string('imagen_path')->nullable();
            $table->boolean('tiene_imagen')->default(false);
            $table->string('tipo')->default('opcion_multiple'); // 'opcion_multiple', 'verdadero_falso'
            $table->decimal('puntos', 5, 2)->default(1.00);
            $table->integer('orden')->default(0);
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
