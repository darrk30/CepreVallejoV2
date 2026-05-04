<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AcademicCycleDetail extends Model
{
    protected $fillable = [
        'academic_cycle_id',
        'nombre',
        'icono',
        'estado',
        'orden',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($detail) {
            if (Auth::check()) {
                $detail->user_create_id = Auth::id();
            }
            if (is_null($detail->orden)) {
                $detail->orden = static::where('academic_cycle_id', $detail->academic_cycle_id)->max('orden') + 1;
            }
        });
    }

    // Relación con el Ciclo Académico (Debes asegurarte de tener la relación inversa en el modelo AcademicCycle)
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AcademicCycle::class, 'academic_cycle_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}