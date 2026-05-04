<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CourseContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'titulo',
        'descripcion',
        'orden',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($content) {
            if (Auth::check()) {
                $content->user_create_id = Auth::id();
            }

            if (is_null($content->orden) || $content->orden === 0) {
                $lastOrder = static::where('course_id', $content->course_id)
                    ->max('orden');

                $content->orden = $lastOrder + 1;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
