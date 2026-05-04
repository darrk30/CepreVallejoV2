<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
