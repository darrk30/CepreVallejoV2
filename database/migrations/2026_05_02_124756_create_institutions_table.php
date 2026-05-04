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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social')->nullable();
            $table->string('ruc', 11)->nullable(); 
            
            // Información de Contacto
            $table->string('whatsapp')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            
            // Identidad Visual y Contenido
            $table->text('nosotros')->nullable(); // Para la sección "Acerca de nosotros"
            $table->string('logo_path')->nullable();
            
            // Redes Sociales (Enlaces)
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            
            // Control
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
