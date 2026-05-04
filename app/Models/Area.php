<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Area extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($area) {
            if (Auth::check()) {
                $area->user_create_id = Auth::id();
            }
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}