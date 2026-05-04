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
        Schema::create('conventions', function (Blueprint $table) {
            $table->id();
            // Datos del Convenio
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->string('periodo')->nullable();
            $table->string('representante')->nullable();
            $table->string('estado_convenio')->default('Vigente');
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
        Schema::dropIfExists('conventions');
    }
};
