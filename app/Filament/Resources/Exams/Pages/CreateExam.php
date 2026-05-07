<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\TeacherCourseContent;
use App\Models\TeacherCourseContentDetail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Crear la Sección (TeacherCourseContent)
        $content = TeacherCourseContent::create([
            'ciclo_course_teacher_id' => $data['ciclo_course_teacher_id'],
            'titulo'                  => $data['content_titulo'],
            'descripcion'             => $data['content_descripcion'] ?? null,
            'estado'                  => 'activo',
            'user_create_id'          => Auth::id(),
        ]);

        // 2. Crear el Tema (TeacherCourseContentDetail) ligado a la sección
        $detail = TeacherCourseContentDetail::create([
            'teacher_course_content_id' => $content->id,
            'titulo'                    => $data['detail_titulo'],
            'descripcion'               => $data['detail_descripcion'] ?? null,
            'estado'                    => 'activo',
            'user_create_id'            => Auth::id(),
        ]);

        // 3. Crear el Examen ligado al detalle
        $exam = static::getModel()::create([
            'teacher_course_content_detail_id' => $detail->id,
            'titulo'                           => $data['titulo'],
            'descripcion'                      => $data['descripcion'] ?? null,
            'duracion_minutos'                 => $data['duracion_minutos'],
            'intentos_maximos'                 => $data['intentos_maximos'],
            'puntaje_minimo'                   => $data['puntaje_minimo'],
            'estado'                           => $data['estado'],
            'user_create_id'                   => Auth::id(),
        ]);

        return $exam;
    }
}