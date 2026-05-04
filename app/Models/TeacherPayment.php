<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TeacherPayment extends Model
{
    protected $fillable = [
        'teacher_id',
        'academic_cycle_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'numero_operacion',
        'comprobante_path',
        'estado',
        'observaciones',
        'user_create_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (Auth::check()) {
                $payment->user_create_id = Auth::id();
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicCycle(): BelongsTo
    {
        return $this->belongsTo(AcademicCycle::class);
    }
}