<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    protected $fillable = [
        'inscription_id',
        'monto',
        'metodo_pago',
        'fecha_vencimiento',
        'fecha_pago',
        'referencia',
        'estado',
        'user_create_id'
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'fecha_pago' => 'datetime',
            'monto' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Al crear, actualizar o eliminar un pago, recalculamos la cuenta del alumno
        static::saved(fn(Payment $payment) => $payment->inscription->recalculateBalances());
        static::deleted(fn(Payment $payment) => $payment->inscription->recalculateBalances());

        static::creating(function ($payment) {
            $payment->user_create_id = Auth::id();
        });
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }
}
