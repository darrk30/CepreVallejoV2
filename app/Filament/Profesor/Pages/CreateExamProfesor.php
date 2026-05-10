<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TeacherCourseContentDetail;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateExamProfesor extends Page implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected string $view = 'filament.profesor.pages.create-exam-profesor';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'virtual-classroom/exams/create/{detailId}';

    // ── Props ────────────────────────────────────────────────
    public int $detailId;
    public ?TeacherCourseContentDetail $detail = null;
    public ?Exam $existingExam = null;

    // ── Form state ───────────────────────────────────────────
    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Solo permite el acceso si el usuario tiene el permiso específico
        return Auth::user()->can('create_exam');
    }

    public function mount(int $detailId): void
    {
        abort_unless(Auth::user()->can('create_exam'), 403);
        $this->detailId = $detailId;

        $this->detail = TeacherCourseContentDetail::with([
            'content.cicloCourseTeacher.cicloCourse.course',
            'exam.questions.options',
        ])->findOrFail($detailId);

        // Verificar que este detail pertenece al profesor autenticado
        $teacherId = Auth::user()?->teacher?->id;
        $assignmentTeacherId = $this->detail->content?->cicloCourseTeacher?->teacher_id;

        if (!$teacherId || $teacherId !== $assignmentTeacherId) {
            abort(403);
        }

        $this->existingExam = $this->detail->exam;

        // Si ya existe un examen → prellenar el formulario para editar
        if ($this->existingExam) {
            $this->form->fill([
                'titulo'            => $this->existingExam->titulo,
                'descripcion'       => $this->existingExam->descripcion,
                'estado'            => $this->existingExam->estado,
                'duracion_minutos'  => $this->existingExam->duracion_minutos,
                'intentos_maximos'  => $this->existingExam->intentos_maximos,
                'puntaje_minimo'    => $this->existingExam->puntaje_minimo,
                'questions'         => $this->existingExam->questions->map(function ($q) {
                    return [
                        'id'               => $q->id,
                        'texto_pregunta'   => $q->texto_pregunta,
                        'puntos'           => $q->puntos,
                        'imagen_path'      => $q->imagen_path,
                        'config_show_image' => (bool) $q->imagen_path,
                        'options'          => $q->options->map(fn($o) => [
                            'id'                    => $o->id,
                            'texto_opcion'          => $o->texto_opcion,
                            'es_correcta'           => (bool) $o->es_correcta,
                            'imagen_path'           => $o->imagen_path,
                            'config_show_image_opt' => (bool) $o->imagen_path,
                        ])->toArray(),
                    ];
                })->toArray(),
            ]);
        } else {
            $this->form->fill([
                'estado'           => 'borrador',
                'duracion_minutos' => 60,
                'intentos_maximos' => 1,
                'puntaje_minimo'   => 10.5,
                'questions'        => [],
            ]);
        }
    }

    // ── Breadcrumb ───────────────────────────────────────────
    public function getBreadcrumbs(): array
    {
        $course = $this->detail?->content?->cicloCourseTeacher?->cicloCourse?->course;
        $courseSlug = $course?->slug;
        $courseName = $course?->nombre ?? 'Curso';

        return [
            VirtualClassroom::getUrl()                                          => 'Mi Aula Virtual',
            $courseSlug
                ? ManageCourseContent::getUrl(['courseSlug' => $courseSlug])
                : '#'                                                           => $courseName,
            '#'                                                                 => $this->existingExam
                ? 'Editar Examen'
                : 'Crear Examen',
        ];
    }

    public function getTitle(): string
    {
        return $this->existingExam ? 'Editar Examen' : 'Crear Examen';
    }

    public function getHeading(): string
    {
        return '';
    }

    // ── Formulario ───────────────────────────────────────────
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // ── Info del contexto (solo lectura) ──────────
                Section::make('Contexto del examen')
                    ->description('El examen quedará vinculado a este tema.')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('seccion')
                            ->label('Sección')
                            ->content(fn() => $this->detail?->content?->titulo ?? '—'),
                        \Filament\Forms\Components\Placeholder::make('tema')
                            ->label('Tema')
                            ->content(fn() => $this->detail?->titulo ?? '—'),
                        \Filament\Forms\Components\Placeholder::make('curso')
                            ->label('Curso')
                            ->content(fn() => $this->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->nombre ?? '—'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(fn() => (bool) $this->existingExam),

                // ── Config del examen ─────────────────────────
                Section::make('Configuración del examen')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('titulo')
                                    ->label('Título del Examen')
                                    ->placeholder('Ej: Examen Parcial I')
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'activo'   => 'Activo',
                                        'borrador' => 'Borrador',
                                    ])
                                    ->default('borrador')
                                    ->required(),

                                TextInput::make('duracion_minutos')
                                    ->label('Duración (minutos)')
                                    ->numeric()
                                    ->default(60)
                                    ->required(),

                                TextInput::make('intentos_maximos')
                                    ->label('Intentos Máximos')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                TextInput::make('puntaje_minimo')
                                    ->label('Nota Aprobatoria')
                                    ->numeric()
                                    ->default(10.5)
                                    ->required(),
                            ]),
                    ]),

                // ── Preguntas ─────────────────────────────────
                Section::make('Banco de Preguntas')
                    ->schema([
                        Repeater::make('questions')
                            ->label('')
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn(array $state): ?string => $state['texto_pregunta'] ?? 'Nueva Pregunta')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Textarea::make('texto_pregunta')
                                            ->label('Enunciado de la Pregunta')
                                            ->required()
                                            ->columnSpan(10),
                                        TextInput::make('puntos')
                                            ->label('Puntos')
                                            ->numeric()
                                            ->default(1)
                                            ->columnSpan(2),
                                    ]),

                                Checkbox::make('config_show_image')
                                    ->label('¿Agregar imagen a la pregunta?')
                                    ->live()
                                    ->afterStateHydrated(function (Checkbox $component, Get $get) {
                                        if ($get('imagen_path')) $component->state(true);
                                    }),

                                FileUpload::make('imagen_path')
                                    ->label('Imagen de apoyo')
                                    ->image()
                                    ->directory('exams/questions')
                                    ->imageEditor()
                                    ->optimize('webp', 80)
                                    ->maxImageWidth(1200)
                                    ->columnSpanFull()
                                    ->visible(fn(Get $get): bool => (bool) $get('config_show_image')),

                                Repeater::make('options')
                                    ->label('Alternativas')
                                    ->grid(2)
                                    ->schema([
                                        Textarea::make('texto_opcion')
                                            ->label('Texto de la Opción')
                                            ->required(),

                                        Grid::make(2)
                                            ->schema([
                                                Checkbox::make('es_correcta')
                                                    ->label('¿Es la correcta?')
                                                    ->columnSpan(1),
                                                Checkbox::make('config_show_image_opt')
                                                    ->label('¿Usar imagen?')
                                                    ->live()
                                                    ->afterStateHydrated(function (Checkbox $component, Get $get) {
                                                        if ($get('imagen_path')) $component->state(true);
                                                    })
                                                    ->columnSpan(1),
                                            ]),

                                        FileUpload::make('imagen_path')
                                            ->label('Imagen de la opción')
                                            ->image()
                                            ->directory('exams/options')
                                            ->imageEditor()
                                            ->optimize('webp', 80)
                                            ->maxImageWidth(1200)
                                            ->visible(fn(Get $get): bool => (bool) $get('config_show_image_opt')),
                                    ])
                                    ->minItems(2)
                                    ->addActionLabel('Añadir Alternativa'),
                            ])
                            ->addActionLabel('Añadir Pregunta al Examen'),
                    ]),
            ])
            ->statePath('data');
    }

    // ── Guardar ───────────────────────────────────────────────
    public function save(): void
    {
        $formData = $this->form->getState();

        DB::transaction(function () use ($formData) {
            $examData = [
                'teacher_course_content_detail_id' => $this->detailId,
                'titulo'           => $formData['titulo'],
                'descripcion'      => $formData['descripcion'] ?? null,
                'estado'           => $formData['estado'],
                'duracion_minutos' => $formData['duracion_minutos'],
                'intentos_maximos' => $formData['intentos_maximos'],
                'puntaje_minimo'   => $formData['puntaje_minimo'],
                'user_create_id'   => Auth::id(),
            ];

            // Crear o actualizar el examen
            if ($this->existingExam) {
                $this->existingExam->update($examData);
                $exam = $this->existingExam;
            } else {
                $exam = Exam::create($examData);
                $this->existingExam = $exam;
            }

            // ── Sincronizar preguntas ─────────────────────────
            $submittedQuestions = $formData['questions'] ?? [];
            $existingQIds = $exam->questions()->pluck('id')->toArray();
            $submittedQIds = collect($submittedQuestions)->pluck('id')->filter()->toArray();

            // Eliminar preguntas que ya no están
            Question::whereIn('id', array_diff($existingQIds, $submittedQIds))->delete();

            foreach ($submittedQuestions as $qData) {
                $question = isset($qData['id'])
                    ? Question::find($qData['id'])
                    : null;

                $qPayload = [
                    'exam_id'        => $exam->id,
                    'texto_pregunta' => $qData['texto_pregunta'],
                    'puntos'         => $qData['puntos'] ?? 1,
                    'imagen_path'    => $qData['imagen_path'] ?? null,
                ];

                if ($question) {
                    $question->update($qPayload);
                } else {
                    $question = Question::create($qPayload);
                }

                // ── Sincronizar opciones ───────────────────────
                $submittedOptions = $qData['options'] ?? [];
                $existingOIds = $question->options()->pluck('id')->toArray();
                $submittedOIds = collect($submittedOptions)->pluck('id')->filter()->toArray();

                Option::whereIn('id', array_diff($existingOIds, $submittedOIds))->delete();

                foreach ($submittedOptions as $oData) {
                    $option = isset($oData['id'])
                        ? Option::find($oData['id'])
                        : null;

                    $oPayload = [
                        'question_id'  => $question->id,
                        'texto_opcion' => $oData['texto_opcion'],
                        'es_correcta'  => (bool) ($oData['es_correcta'] ?? false),
                        'imagen_path'  => $oData['imagen_path'] ?? null,
                    ];

                    if ($option) {
                        $option->update($oPayload);
                    } else {
                        Option::create($oPayload);
                    }
                }
            }
        });

        Notification::make()
            ->title($this->existingExam ? 'Examen actualizado correctamente' : 'Examen creado correctamente')
            ->success()
            ->send();

        // Redirigir al curso
        $courseSlug = $this->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->slug;

        if ($courseSlug) {
            $this->redirect(ManageCourseContent::getUrl(['courseSlug' => $courseSlug]));
        } else {
            $this->redirect(VirtualClassroom::getUrl());
        }
    }

    // ── Cancelar ─────────────────────────────────────────────
    public function cancel(): void
    {
        $courseSlug = $this->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->slug;

        if ($courseSlug) {
            $this->redirect(ManageCourseContent::getUrl(['courseSlug' => $courseSlug]));
        } else {
            $this->redirect(VirtualClassroom::getUrl());
        }
    }
}
