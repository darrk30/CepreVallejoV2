<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * PASO 1: Llenar el formulario con los datos del contenido relacionado
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Accedemos al contenido a través de la relación definida en el modelo Exam
        $content = $this->record->teacherCourseContent;

        if ($content) {
            $data['ciclo_course_teacher_id'] = $content->ciclo_course_teacher_id;
            $data['content_titulo'] = $content->titulo;
            $data['content_descripcion'] = $content->descripcion;
        }

        return $data;
    }

    /**
     * PASO 2: Actualizar tanto el Contenido como el Examen al guardar
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Actualizamos el registro de TeacherCourseContent relacionado
        $record->teacherCourseContent->update([
            'ciclo_course_teacher_id' => $data['ciclo_course_teacher_id'],
            'titulo' => $data['content_titulo'],
            'descripcion' => $data['content_descripcion'],
        ]);

        // 2. Actualizamos los datos propios del Examen (preguntas, duración, etc.)
        $record->update($data);

        return $record;
    }
}
