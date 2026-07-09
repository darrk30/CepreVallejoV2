<?php

namespace App\Filament\Pages;

use App\Models\CicloCourseTeacher;
use App\Models\TeacherCourseContent;
use App\Models\TeacherCourseContentDetail;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ManageCourseContentAdmin extends Page implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.pages.manage-course-content-admin';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'cursos-admin/{assignmentId}';

    public int $assignmentId;
    public ?CicloCourseTeacher $assignment = null;

    public static function canAccess(): bool
    {
        return Auth::user()->can('manage_all_course_content');
    }

    public function mount(int $assignmentId): void
    {
        $this->assignmentId = $assignmentId;

        $this->assignment = CicloCourseTeacher::with([
            'cicloCourse.course',
            'cicloCourse.academicCycle',
            'teacher.user',
            'turno',
        ])->findOrFail($assignmentId);
    }

    public function getBreadcrumbs(): array
    {
        $courseName = $this->assignment?->cicloCourse?->course?->nombre ?? 'Curso';

        return [
            CoursesOverview::getUrl() => 'Cursos del Ciclo',
            '#' => $courseName,
        ];
    }

    public function getTitle(): string
    {
        return $this->assignment?->cicloCourse?->course?->nombre ?? 'Curso';
    }

    public function getHeading(): string
    {
        return '';
    }

    /* ── PROPIEDADES ── */

    public function getSectionsProperty()
    {
        return TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId)
            ->with([
                'details' => fn($q) => $q->orderBy('orden'),
                'details.exam',
            ])
            ->orderBy('orden')
            ->get();
    }

    /* ── ACCIONES DE CABECERA ── */

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Sección')
                ->model(TeacherCourseContent::class)
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
                    $data['user_create_id'] = Auth::id();
                    return $data;
                })
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    /* ── REORDEN — SECCIONES (drag & drop desde JS) ── */

    #[On('reorderSections')]
    public function reorderSections(array $ids): void
    {
        abort_unless(Auth::user()->can('manage_all_course_content'), 403);
        foreach ($ids as $index => $id) {
            TeacherCourseContent::where('id', $id)
                ->where('ciclo_course_teacher_id', $this->assignmentId)
                ->update(['orden' => $index + 1]);
        }
    }

    /* ── REORDEN — DETALLES (drag & drop desde JS) ── */

    #[On('reorderDetails')]
    public function reorderDetails(int $sectionId, array $ids): void
    {
        abort_unless(Auth::user()->can('manage_all_course_content'), 403);
        foreach ($ids as $index => $id) {
            TeacherCourseContentDetail::where('id', $id)
                ->where('teacher_course_content_id', $sectionId)
                ->update(['orden' => $index + 1]);
        }
    }

    /* ── ACCIONES DE SECCIÓN ── */

    public function editSectionAction(): Action
    {
        return EditAction::make('editSection')
            ->record(fn(array $arguments) => TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId)->find($arguments['section_id']))
            ->schema([
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
            ->record(fn(array $arguments) => TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId)->find($arguments['section_id']))
            ->iconButton();
    }

    /* ── ACCIONES DE DETALLE ── */

    public function createSubtopicAction(): Action
    {
        return Action::make('createSubtopic')
            ->label('Nuevo tema')
            ->schema([
                TextInput::make('titulo')
                    ->label('Título del tema')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('descripcion')
                    ->label('Descripción')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                    ])
                    ->columnSpanFull(),
                FileUpload::make('archivo_path')
                    ->label('Archivo (PDF, PPT, etc.)')
                    ->directory('material-cepre')
                    ->preserveFilenames()
                    ->maxSize(20480),
                TextInput::make('url_video')
                    ->label('URL de YouTube')
                    ->url()
                    ->placeholder('https://youtube.com/watch?v=...'),
            ])
            ->action(function (array $data, array $arguments): void {
                $section = TeacherCourseContent::where('ciclo_course_teacher_id', $this->assignmentId)
                    ->findOrFail($arguments['section_id']);

                $data['teacher_course_content_id'] = $section->id;
                $data['orden'] = TeacherCourseContentDetail::where('teacher_course_content_id', $section->id)->count() + 1;
                $data['user_create_id'] = Auth::id();
                TeacherCourseContentDetail::create($data);
            })
            ->icon('heroicon-m-plus-small')
            ->iconButton()
            ->color('gray');
    }

    public function editSubtopicAction(): Action
    {
        return EditAction::make('editSubtopic')
            ->record(fn(array $arguments) => TeacherCourseContentDetail::whereHas(
                'content',
                fn($q) => $q->where('ciclo_course_teacher_id', $this->assignmentId)
            )->find($arguments['subtopic_id']))
            ->schema([
                TextInput::make('titulo')
                    ->label('Título del tema')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('descripcion')
                    ->label('Descripción')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'link',
                    ])
                    ->columnSpanFull(),
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
            ->record(fn(array $arguments) => TeacherCourseContentDetail::whereHas(
                'content',
                fn($q) => $q->where('ciclo_course_teacher_id', $this->assignmentId)
            )->find($arguments['subtopic_id']))
            ->iconButton();
    }

    /* ── HELPERS ── */

    public function getEmbedUrl(?string $url): string
    {
        if (!$url) return '';

        if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (preg_match('/youtu\.be\/([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        return $url;
    }
}
