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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('imagen_desktop_path'); // Obligatorio
            $table->string('imagen_mobile_path')->nullable(); // Para celulares
            $table->string('enlace')->nullable(); // Ej: "https://midominio.com/ciclos"
            $table->string('tipo')->default('publico'); // publico y interno
            $table->integer('orden')->default(0); // Para poder arrastrarlos en Filament
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
        Schema::dropIfExists('banners');
    }
};
