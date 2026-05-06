<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TeacherCourseContentDetail extends Model
{
    protected $fillable = [
        'teacher_course_content_id',
        'titulo',
        'descripcion',
        'archivo_path',
        'url_video',
        'orden',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_create_id = Auth::id();
            }
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(TeacherCourseContent::class, 'teacher_course_content_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}