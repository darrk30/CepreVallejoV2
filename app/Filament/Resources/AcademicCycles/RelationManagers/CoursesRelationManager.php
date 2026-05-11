<?php

namespace App\Filament\Resources\AcademicCycles\RelationManagers;

use App\Models\CicloCourse;
use App\Models\Teacher;
use App\Models\Turno;
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
                    ->fillForm(fn(Model $record): array => [
                        'cicloCourseTeachers' => $record->pivot->cicloCourseTeachers->toArray(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $pivot = $record->pivot;

                        // 1. Obtenemos los IDs de los docentes que vienen del formulario
                        $docentesNuevosIds = collect($data['cicloCourseTeachers'])->pluck('teacher_id')->toArray();

                        // 2. ELIMINAMOS solo a los docentes que fueron quitados del Repeater
                        // Esto protege a los que se quedan, manteniendo su ID intacto.
                        $pivot->cicloCourseTeachers()
                            ->whereNotIn('teacher_id', $docentesNuevosIds)
                            ->delete();

                        // 3. ACTUALIZAMOS O CREAMOS (Sync)
                        if (!empty($data['cicloCourseTeachers'])) {
                            foreach ($data['cicloCourseTeachers'] as $item) {
                                // updateOrCreate busca por teacher_id. 
                                // Si existe, solo cambia el turno_id (el ID no cambia, el contenido se salva).
                                // Si no existe, crea uno nuevo.
                                $pivot->cicloCourseTeachers()->updateOrCreate(
                                    ['teacher_id' => $item['teacher_id']], // Condición de búsqueda
                                    [
                                        'turno_id' => $item['turno_id'],   // Datos a actualizar
                                        'user_create_id' => Auth::id(),
                                        'estado' => 'Activo',
                                    ]
                                );
                            }
                        }
                    })
                    ->schema([
                        Repeater::make('cicloCourseTeachers')
                            ->label('Docentes Asignados')
                            ->schema([
                                Select::make('teacher_id')
                                    ->label('Seleccionar Docente')
                                    ->options(Teacher::with('user')->get()->pluck('user.name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(), // Evita duplicar el mismo docente
                                Select::make('turno_id')
                                    ->label('Turno')
                                    ->options(Turno::all()->pluck('nombre', 'id'))
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Añadir otro docente'),
                    ]),

                DetachAction::make(),
            ]);
    }
}
