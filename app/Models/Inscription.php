<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Inscription extends Model
{
    protected $fillable = [
        'codigo',
        'student_id',
        'academic_cycle_id',
        'turno_id',
        'fecha_inscripcion',
        'monto_pagado',
        'saldo',
        'estado_pago',
        'user_create_id',
    ];

    /**
     * Casting de atributos.
     */
    protected function casts(): array
    {
        return [
            'fecha_inscripcion' => 'datetime',
            'monto_pagado' => 'decimal:2',
            'saldo' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Generación automática del código de inscripción al crear el registro.
     * Ejemplo: INS-2026-0001
     */
    protected static function booted(): void
    {
        static::creating(function (Inscription $inscription) {
            $year = now()->year;
            $lastId = static::whereYear('created_at', $year)->max('id') ?? 0;
            $number = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $inscription->codigo = "INS-{$year}-{$number}";
        });
    }

    public function recalculateBalances(): void
    {
        // Sumamos solo los pagos que ya están en estado 'pagado'
        $totalPagado = $this->payments()->where('estado', 'pagado')->sum('monto');

        // Obtenemos el precio del ciclo (asumiendo que está en la relación)
        $precioCiclo = $this->academicCycle->precio ?? 0;

        $this->update([
            'monto_pagado' => $totalPagado,
            'saldo' => max(0, $precioCiclo - $totalPagado),
            'estado_pago' => $this->determinePaymentStatus($precioCiclo, $totalPagado),
        ]);
    }

    protected function determinePaymentStatus($total, $pagado): string
    {
        if ($pagado <= 0) return 'pendiente';
        if ($pagado >= $total) return 'pagado';
        return 'parcial';
    }

    /**
     * Relación con el estudiante.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relación con el ciclo académico.
     */
    public function academicCycle(): BelongsTo
    {
        return $this->belongsTo(AcademicCycle::class, 'academic_cycle_id');
    }

    /**
     * Relación con el usuario que creó la inscripción (Auditoría).
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }
}
