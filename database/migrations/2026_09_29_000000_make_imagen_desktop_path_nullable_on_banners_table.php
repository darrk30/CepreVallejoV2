<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un banner de tipo "video de TikTok" no necesita imagen: la miniatura
     * se pide a la API oEmbed de TikTok al vuelo, sin guardarla. Antes esta
     * columna era NOT NULL, lo que rompía el guardado aunque el formulario
     * ya no la exigiera para ese caso.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('imagen_desktop_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('imagen_desktop_path')->nullable(false)->change();
        });
    }
};
