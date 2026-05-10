<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Usamos $table que es lo estándar
            $table->text('mision')->nullable()->after('nosotros');
            $table->text('vision')->nullable()->after('mision');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['mision', 'vision']);
        });
    }
};