<?php

namespace App\Filament\Resources\AcademicCycles\RelationManagers;

use App\Models\CicloCourse;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Turno;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
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
            // El join contra la tabla pivote (ciclo_course) no aplica
            // automáticamente el scope de SoftDeletes del modelo CicloCourse
            // (BelongsToMany arma el JOIN por SQL directo), así que hay que
            // excluir manualmente los cursos desvinculados (soft-deleted).
            ->modifyQueryUsing(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereNull('ciclo_course.deleted_at'))
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
                    // No usamos el select automático de Filament: internamente
                    // excluye cursos con whereDoesntHave sobre la relación de
                    // pivote, y eso no distingue un vínculo activo de uno
                    // soft-deleted (desvinculado) — un curso desvinculado no
                    // volvía a aparecer como disponible. Con nuestro propio
                    // select solo excluimos los que tienen un vínculo ACTIVO.
                    ->schema(function (): array {
                        $vinculadosActivos = CicloCourse::where('ciclo_id', $this->getOwnerRecord()->id)
                            ->pluck('course_id');

                        return [
                            Select::make('recordId')
                                ->label('Curso')
                                ->options(Course::whereNotIn('id', $vinculadosActivos)->pluck('nombre', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('estado')
                                ->options([
                                    'Activo' => 'Activo',
                                    'Inactivo' => 'Inactivo',
                                ])
                                ->default('Activo')
                                ->required(),
                        ];
                    })
                    ->action(function (array $data): void {
                        // Si este curso ya estuvo vinculado a este ciclo antes (y fue
                        // desvinculado), reactivamos ese mismo registro en vez de crear
                        // uno nuevo: así todo su contenido (profesores, secciones,
                        // temas, exámenes) reaparece tal como estaba.
                        $cicloCourse = CicloCourse::withTrashed()
                            ->where('ciclo_id', $this->getOwnerRecord()->id)
                            ->where('course_id', $data['recordId'])
                            ->first();

                        if ($cicloCourse) {
                            if ($cicloCourse->trashed()) {
                                $cicloCourse->restore();
                            }
                            $cicloCourse->update(['estado' => $data['estado']]);
                            return;
                        }

                        CicloCourse::create([
                            'ciclo_id' => $this->getOwnerRecord()->id,
                            'course_id' => $data['recordId'],
                            'estado' => $data['estado'],
                            'user_create_id' => Auth::id(),
                        ]);
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

                        // 2. DESVINCULAMOS (soft delete) solo a los docentes que fueron
                        // quitados del Repeater. No se borra nada físicamente: su
                        // contenido (secciones, temas, exámenes) queda intacto y
                        // reaparece si se vuelve a asignar al mismo docente y turno.
                        $pivot->cicloCourseTeachers()
                            ->whereNotIn('teacher_id', $docentesNuevosIds)
                            ->delete();

                        // 3. ACTUALIZAMOS, RESTAURAMOS O CREAMOS
                        if (!empty($data['cicloCourseTeachers'])) {
                            foreach ($data['cicloCourseTeachers'] as $item) {
                                // Buscamos incluyendo los soft-deleted: si este docente ya
                                // había sido asignado antes a este curso, reutilizamos ese
                                // mismo registro (y su contenido) en vez de crear uno nuevo.
                                $asignacion = $pivot->cicloCourseTeachers()
                                    ->withTrashed()
                                    ->firstOrNew(['teacher_id' => $item['teacher_id']]);

                                if ($asignacion->trashed()) {
                                    $asignacion->restore();
                                }

                                $asignacion->turno_id = $item['turno_id'];
                                $asignacion->user_create_id = Auth::id();
                                $asignacion->estado = 'Activo';
                                $asignacion->save();
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

                Action::make('detach')
                    ->label('Desvincular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desvincular curso del ciclo')
                    ->modalDescription('El curso se quitará de este ciclo, pero su contenido (secciones, temas, exámenes de cada profesor) no se borra: se conserva y reaparece automáticamente si vuelves a vincular este mismo curso a este ciclo.')
                    ->modalSubmitActionLabel('Sí, desvincular')
                    ->action(function (Model $record): void {
                        // Soft delete real (vía el modelo Eloquent del pivot), no un
                        // ->detach() crudo: así no se dispara la cascada de borrado
                        // hacia ciclo_course_teacher / contenido / exámenes.
                        $record->pivot->delete();
                    }),
            ]);
    }
}
