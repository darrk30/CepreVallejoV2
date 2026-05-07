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
     * Pre-llenar el formulario con los datos del detalle y su sección padre.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Detalle (tema) al que pertenece el examen
        $detail = $this->record->detail; // relación en Exam

        if ($detail) {
            $data['detail_titulo']      = $detail->titulo;
            $data['detail_descripcion'] = $detail->descripcion;

            // Sección (content) padre del detalle
            $content = $detail->content; // relación en TeacherCourseContentDetail
            if ($content) {
                $data['ciclo_course_teacher_id'] = $content->ciclo_course_teacher_id;
                $data['content_titulo']          = $content->titulo;
                $data['content_descripcion']     = $content->descripcion;
            }
        }

        return $data;
    }

    /**
     * Actualizar los 3 niveles al guardar.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $detail  = $record->detail;
        $content = $detail?->content;

        // 1. Actualizar la Sección
        if ($content) {
            $content->update([
                'ciclo_course_teacher_id' => $data['ciclo_course_teacher_id'],
                'titulo'                  => $data['content_titulo'],
                'descripcion'             => $data['content_descripcion'] ?? null,
            ]);
        }

        // 2. Actualizar el Tema
        if ($detail) {
            $detail->update([
                'titulo'      => $data['detail_titulo'],
                'descripcion' => $data['detail_descripcion'] ?? null,
            ]);
        }

        // 3. Actualizar el Examen
        $record->update([
            'titulo'            => $data['titulo'],
            'descripcion'       => $data['descripcion'] ?? null,
            'duracion_minutos'  => $data['duracion_minutos'],
            'intentos_maximos'  => $data['intentos_maximos'],
            'puntaje_minimo'    => $data['puntaje_minimo'],
            'estado'            => $data['estado'],
        ]);

        return $record;
    }
}