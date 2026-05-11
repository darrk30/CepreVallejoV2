<?php

namespace App\Filament\Profesor\Pages;

use App\Models\CicloCourseTeacher;
use App\Models\TeacherCourseContent;
use App\Models\TeacherCourseContentDetail;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class ManageCourseContent extends Page implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.profesor.pages.manage-course-content';
    protected static bool $shouldRegisterNavigation = false;

    // ── Slug con parámetro de ruta ────────────────────────────
    protected static ?string $slug = 'virtual-classroom/{courseSlug}';

    public ?string $courseSlug  = null;
    public ?int    $assignmentId = null;

    public function mount(string $courseSlug): void
    {
        $this->courseSlug = $courseSlug;
        $user = auth()->user();

        $query = CicloCourseTeacher::query()
            ->whereHas('cicloCourse.course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug);
            });

        if ($user->teacher) {
            $query->where('teacher_id', $user->teacher->id);
        } elseif ($user->student) {
            $lastInscription = $user->student->inscriptions()
                ->where('estado_pago', '!=', 'Cancelado') // Filtro de pago
                ->latest()
                ->first();

            if ($lastInscription && $lastInscription->turno_id) {
                // Buscamos la asignación que coincida con el turno del alumno
                $query->where('turno_id', $lastInscription->turno_id);
            } else {
                $this->assignmentId = null;
                return;
            }
        }

        $assignment = $query->first();
        $this->assignmentId = $assignment?->id;
    }

    // ── Breadcrumb ────────────────────────────────────────────
    public function getBreadcrumbs(): array
    {
        $assignment = $this->assignment;
        $courseName = $assignment?->cicloCourse?->course?->nombre ?? 'Curso';
        $cycleName  = $assignment?->cicloCourse?->academicCycle?->nombre ?? 'Ciclo';

        return [
            VirtualClassroom::getUrl() => 'Mi Aula Virtual',
            '#'                        => "{$cycleName} · {$courseName}",
        ];
    }

    // ── Título de la página (tab del navegador) ───────────────

    public function getTitle(): string
    {
        return $this->assignment?->cicloCourse?->course?->nombre ?? 'Sin Asignación';
    }

    /* ── PROPIEDADES ── */

    public function getAssignmentProperty()
    {
        return CicloCourseTeacher::with([
            'cicloCourse.course',
            'cicloCourse.academicCycle',
        ])->find($this->assignmentId);
    }

    public function getSectionsProperty()
    {
        if (!$this->assignmentId) {
            return collect();
        }

        $user = auth()->user();

        // Iniciamos la consulta para las secciones de ESTA asignación
        $query = TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId);

        // IMPORTANTE:
        // Si es PROFESOR, solo mostramos lo que ÉL creó (para que no vea lo de otros profes del mismo curso)
        // Si es ALUMNO, mostramos TODO lo que el profesor asignado a su turno haya subido
        if ($user->teacher) {
            $query->where('user_create_id', $user->id);
        }

        return $query->with([
            'details' => function ($q) use ($user) {
                // Aplicamos la misma lógica a los temas (subtopics)
                if ($user->teacher) {
                    $q->where('user_create_id', $user->id);
                }
                $q->orderBy('orden');
            },
            'details.exam',
        ])
            ->orderBy('orden')
            ->get();
    }
    /* ── ACCIONES DE CABECERA ── */

    protected function getHeaderActions(): array
    {
        if (!$this->assignmentId) {
            return [];
        }

        return [
            CreateAction::make()
                ->label('Nueva Sección')
                ->model(TeacherCourseContent::class)
                ->visible(fn() => auth()->user()->can('create_section'))
                ->schema([
                    TextInput::make('titulo')
                        ->label('Nombre de la Sección')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('descripcion')
                        ->label('Descripción (opcional)')
                        ->rows(3),
                ])
                ->mutateDataUsing(function (array $data): array {
                    $data['ciclo_course_teacher_id'] = $this->assignmentId;
                    $data['orden'] = TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId)->count() + 1;
                    $data['user_create_id'] = auth()->id();
                    return $data;
                })
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    /* ── REORDEN — SECCIONES (drag & drop desde JS) ── */

    #[On('reorderSections')]
    public function reorderSections(array $ids): void
    {
        abort_unless(auth()->user()->can('order_section'), 403);
        foreach ($ids as $index => $id) {
            TeacherCourseContent::where('id', $id)->update(['orden' => $index + 1]);
        }
    }

    /* ── REORDEN — DETALLES (drag & drop desde JS) ── */

    #[On('reorderDetails')]
    public function reorderDetails(int $sectionId, array $ids): void
    {
        abort_unless(auth()->user()->can('order_topic'), 403);
        foreach ($ids as $index => $id) {
            TeacherCourseContentDetail::where('id', $id)->update(['orden' => $index + 1]);
        }
    }

    /* ── REORDEN HEREDADO (botones flecha) — mantenido por compatibilidad ── */

    public function moveItem(string $type, int $id, string $direction): void
    {
        $model = $type === 'section'
            ? TeacherCourseContent::find($id)
            : TeacherCourseContentDetail::find($id);

        $parentIdField = $type === 'section'
            ? 'ciclo_course_teacher_id'
            : 'teacher_course_content_id';

        $parentId     = $model->$parentIdField;
        $currentOrder = $model->orden;

        $swapWith = $direction === 'up'
            ? $model::where($parentIdField, $parentId)->where('orden', '<', $currentOrder)->orderByDesc('orden')->first()
            : $model::where($parentIdField, $parentId)->where('orden', '>', $currentOrder)->orderBy('orden')->first();

        if ($swapWith) {
            $model->update(['orden' => $swapWith->orden]);
            $swapWith->update(['orden' => $currentOrder]);
        }
    }

    /* ── ACCIONES DE SECCIÓN ── */

    public function editSectionAction(): Action
    {
        return EditAction::make('editSection')
            ->record(fn(array $arguments) => TeacherCourseContent::find($arguments['section_id']))
            ->visible(fn() => auth()->user()->can('update_section'))
            ->form([
                TextInput::make('titulo')
                    ->label('Nombre de la Sección')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3),
            ])
            ->icon('heroicon-m-pencil-square')
            ->iconButton();
    }

    public function deleteSectionAction(): Action
    {
        return DeleteAction::make('deleteSection')
            ->record(fn(array $arguments) => TeacherCourseContent::find($arguments['section_id']))
            ->visible(fn() => auth()->user()->can('delete_section'))
            ->before(fn() => abort_unless(auth()->user()->can('delete_section'), 403))
            ->iconButton();
    }

    /* ── ACCIONES DE DETALLE ── */

    public function createSubtopicAction(): Action
    {
        return Action::make('createSubtopic')
            ->label('Nuevo tema')
            ->visible(fn() => auth()->user()->can('create_topic'))
            ->schema([
                TextInput::make('titulo')
                    ->label('Título del tema')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(2),
                FileUpload::make('archivo_path')
                    ->label('Archivo (PDF, PPT, etc.)')
                    ->directory('material-cepre')
                    ->preserveFilenames()   // ← guarda el nombre original
                    ->maxSize(20480),
                TextInput::make('url_video')
                    ->label('URL de YouTube')
                    ->url()
                    ->placeholder('https://youtube.com/watch?v=...'),
            ])
            ->action(function (array $data, array $arguments): void {
                $data['teacher_course_content_id'] = $arguments['section_id'];
                $data['orden'] = TeacherCourseContentDetail::where('teacher_course_content_id', $arguments['section_id'])->count() + 1;
                $data['user_create_id'] = auth()->id();
                TeacherCourseContentDetail::create($data);
            })
            ->icon('heroicon-m-plus-small')
            ->iconButton()
            ->color('gray');
    }

    public function editSubtopicAction(): Action
    {
        return EditAction::make('editSubtopic')
            ->record(fn(array $arguments) => TeacherCourseContentDetail::find($arguments['subtopic_id']))
            ->visible(fn() => auth()->user()->can('update_topic'))
            ->schema([
                TextInput::make('titulo')
                    ->label('Título del tema')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(2),
                FileUpload::make('archivo_path')
                    ->label('Archivo')
                    ->directory('material-cepre')
                    ->preserveFilenames(),
                TextInput::make('url_video')
                    ->label('URL de YouTube')
                    ->url(),
            ])
            ->icon('heroicon-m-pencil-square')
            ->iconButton();
    }

    public function deleteSubtopicAction(): Action
    {
        return DeleteAction::make('deleteSubtopic')
            ->record(fn(array $arguments) => TeacherCourseContentDetail::find($arguments['subtopic_id']))
            ->visible(fn() => auth()->user()->can('delete_topic'))
            ->before(fn() => abort_unless(auth()->user()->can('delete_topic'), 403))
            ->iconButton();
    }

    /* ── HELPERS ── */

    public function getEmbedUrl(?string $url): string
    {
        if (!$url) return '';

        // youtube.com/watch?v=ID
        if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // youtu.be/ID
        if (preg_match('/youtu\.be\/([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // youtube.com/embed/ID (ya es embed)
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        return $url;
    }

    public function manageExamAction(): Action
    {
        return Action::make('manageExam')
            ->visible(function (array $arguments) {
                $hasExam = TeacherCourseContentDetail::find($arguments['subtopic_id'])?->exam;
                // Si ya tiene examen, necesita permiso de editar, si no, de crear
                return $hasExam
                    ? auth()->user()->can('update_exam')
                    : auth()->user()->can('create_exam');
            })
            ->action(function (array $arguments): void {
                $detailId = $arguments['subtopic_id'];
                $hasExam = TeacherCourseContentDetail::find($detailId)?->exam;

                // Validación estricta antes de redireccionar
                abort_unless(
                    $hasExam ? auth()->user()->can('update_exam') : auth()->user()->can('create_exam'),
                    403
                );

                $this->redirect(CreateExamProfesor::getUrl(['detailId' => $detailId]));
            })
            // ... iconos y colores iguales
            ->iconButton()
            ->color('warning');
    }

    public function getHeading(): string
    {
        return '';
    }
}
