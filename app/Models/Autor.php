<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $fillable = [
        'nombre',
        'imagen',
        'orden',
        'estado',
    ];

    public function podcasts()
    {
        return $this->hasMany(Podcast::class, 'autor_id');
    }
}
