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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Ej: INS-2026-0001
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_cycle_id')->constrained('academic_cycles')->onDelete('cascade');
            $table->dateTime('fecha_inscripcion');
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('saldo', 10, 2);
            $table->string('estado_pago')->default('pendiente');
            $table->foreignId('user_create_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
