<?php

namespace App\Filament\Resources\AcademicCycles\RelationManagers;

use App\Models\CicloCourse;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Curso')
                    ->searchable(),

                TextColumn::make('docentes_lista')
                    ->label('Docentes')
                    ->getStateUsing(function (Model $record): array {
                        if (! $record->pivot || ! $record->pivot->cicloCourseTeachers) {
                            return [];
                        }

                        return $record->pivot->cicloCourseTeachers
                            ->map(function ($asignacion) {
                                // El salto extra: de teacher a user y luego a name
                                return $asignacion->teacher?->user?->name ?? 'Docente sin usuario';
                            })
                            ->toArray();
                    })
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('pivot.estado')
                    ->label('Estado en Ciclo')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'Activo' ? 'success' : 'warning'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Asignar Curso')
                    ->preloadRecordSelect()
                    ->schema(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                            ])
                            ->default('Activo')
                            ->required(),
                    ])
                    ->mutateDataUsing(function (array $data): array {
                        // Inyectamos el ID del usuario autenticado manualmente
                        $data['user_create_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('gestionarDocentes')
                    ->label('Gestionar Docentes')
                    ->icon('heroicon-m-users')
                    ->modalHeading('Asignar Plana Docente')

                    // 1. Cargamos la data
                    ->fillForm(fn(Model $record): array => [
                        'cicloCourseTeachers' => $record->pivot->cicloCourseTeachers->toArray(),
                    ])

                    // 2. Guardamos (Ya no enviamos el campo 'rol')
                    ->action(function (Model $record, array $data) {
                        $pivot = $record->pivot;

                        $pivot->cicloCourseTeachers()->delete();

                        if (!empty($data['cicloCourseTeachers'])) {
                            foreach ($data['cicloCourseTeachers'] as $item) {
                                $pivot->cicloCourseTeachers()->create([
                                    'teacher_id' => $item['teacher_id'],
                                    'user_create_id' => Auth::id(),
                                    'estado' => 'Activo', // Manteniendo tu estándar de estado
                                ]);
                            }
                        }
                    })

                    // 3. Formulario simplificado
                    ->schema([
                        Repeater::make('cicloCourseTeachers')
                            ->label('Docentes Asignados')
                            ->schema([
                                Select::make('teacher_id')
                                    ->label('Seleccionar Docente')
                                    ->options(
                                        Teacher::with('user')->get()->pluck('user.name', 'id')
                                    )
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(1) // Lo pasamos a 1 columna para que se vea más limpio
                            ->addActionLabel('Añadir otro docente'),
                    ]),

                DetachAction::make(),
            ]);
    }
}
