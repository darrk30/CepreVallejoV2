<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\TeacherCourseContent;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $content = TeacherCourseContent::create([
            'ciclo_course_teacher_id' => $data['ciclo_course_teacher_id'],
            'titulo'                  => $data['content_titulo'],
            'descripcion'             => $data['content_descripcion'],
            'estado'                  => 'activo',
            'user_create_id'          => Auth::id(),
        ]);
        $data['teacher_course_content_id'] = $content->id;
        return static::getModel()::create($data);
    }
}
