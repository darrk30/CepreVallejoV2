<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'texto_pregunta',
        'tipo',
        'puntos',
        'orden',
        'user_create_id', 
        'imagen_path',
        'tiene_imagen',
    ];

    protected static function booted()
    {
        static::creating(fn($model) => $model->user_create_id = Auth::id());
    }

    protected $casts = [
        'tiene_imagen' => 'boolean',
        'puntos' => 'float',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('id');
    }
}
