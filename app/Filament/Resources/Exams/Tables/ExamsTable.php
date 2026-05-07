<?php

namespace App\Filament\Resources\Exams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Asignación completa (Ciclo - Curso - Profesor)
                TextColumn::make('asignacion_detallada')
                    ->label('Asignación')
                    ->getStateUsing(function ($record) {
                        $leccion = $record->teacherCourseContent;
                        if (!$leccion) return 'Sin lección vinculada';

                        $item = $leccion->cicloCourseTeacher;
                        if (!$item) return 'Lección sin asignación';

                        $ciclo = $item->cicloCourse?->academicCycle?->nombre ?? 'S/C';
                        $curso = $item->cicloCourse?->course?->nombre ?? 'S/C';
                        $profe = $item->teacher?->user?->name ?? 'S/P';

                        return "{$ciclo} - {$curso} - {$profe}";
                    })
                    ->description(fn($record) => $record->teacherCourseContent?->titulo ?? 'Sin tema')
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('teacherCourseContent.cicloCourseTeacher.teacher.user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('teacherCourseContent.cicloCourseTeacher.cicloCourse.course', function ($q) use ($search) {
                            $q->where('nombre', 'like', "%{$search}%");
                        });
                    }),

                // 2. Nombre del examen
                TextColumn::make('titulo')
                    ->label('Examen')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                // 3. Cantidad de preguntas
                TextColumn::make('questions_count')
                    ->label('Preg.')
                    ->counts('questions')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                // 4. Participantes
                TextColumn::make('participants_count')
                    ->label('Participantes')
                    ->counts('participants')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                // 5. Estado
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => 'borrador',
                        'success' => 'activo',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                // 6. Duración e Intentos
                TextColumn::make('duracion_minutos')
                    ->label('Duración')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('intentos_maximos')
                    ->label('Max. Intentos')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'activo'   => 'Activo',
                        'borrador' => 'Borrador',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}