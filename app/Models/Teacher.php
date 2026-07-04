<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Teacher extends Model
{
    protected $fillable = [
        'dni',
        'telefono',
        'biografia',
        'estado',
        'imagen_path',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('home.page.data'));
        static::deleted(fn () => Cache::forget('home.page.data'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class);
    }

    public function payments()
    {
        return $this->hasMany(TeacherPayment::class);
    }
}
