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
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('url_audio', 500); 
            $table->string('imagen_portada')->nullable();
            $table->integer('duracion_minutos')->nullable();
            $table->integer('orden')->nullable();
            $table->unsignedInteger('contador_reproducciones')->default(0);
            $table->boolean('estado')->default(true);
            $table->foreignId('autor_id')->nullable()->constrained('autors')->nullOnDelete();
            $table->foreignId('user_create_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
