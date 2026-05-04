<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $fillable = [
        'nombre',
        'estado'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'specialty_teacher');
    }
}
