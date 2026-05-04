<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Banner extends Model
{
    protected $fillable = [
        'imagen_desktop_path',
        'imagen_mobile_path',
        'enlace',
        'tipo',
        'orden',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($banner) {
            if (Auth::check()) {
                $banner->user_create_id = Auth::id();
            }
            
            if (is_null($banner->orden)) {
                $banner->orden = static::max('orden') + 1;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}