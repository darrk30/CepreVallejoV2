<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Models\CicloCourseTeacher;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    // ── PASO 1: SECCIÓN ──────────────────────────────────
                    Step::make('Sección')
                        ->description('Selecciona la asignación y crea la sección.')
                        ->schema([
                            Select::make('ciclo_course_teacher_id')
                                ->label('Asignación: Ciclo - Curso - Profesor')
                                ->options(function () {
                                    return CicloCourseTeacher::with([
                                        'teacher.user',
                                        'cicloCourse.course',
                                        'cicloCourse.academicCycle',
                                    ])
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            $ciclo = $item->cicloCourse->academicCycle->nombre ?? 'Ciclo S/N';
                                            $curso = $item->cicloCourse->course->nombre ?? 'Curso S/N';
                                            $profe = $item->teacher->user->name ?? 'Profesor S/N';
                                            return [$item->id => "{$ciclo} - {$curso} - {$profe}"];
                                        });
                                })
                                ->searchable()
                                ->required()
                                ->columnSpanFull(),

                            TextInput::make('content_titulo')
                                ->label('Título de la Sección')
                                ->placeholder('Ej: Semana 04')
                                ->required(),

                            Textarea::make('content_descripcion')
                                ->label('Descripción de la Sección')
                                ->rows(2),
                        ]),

                    // ── PASO 2: TEMA ─────────────────────────────────────
                    Step::make('Tema')
                        ->description('Crea el tema específico al que pertenece el examen.')
                        ->schema([
                            TextInput::make('detail_titulo')
                                ->label('Título del Tema')
                                ->placeholder('Ej: Ecuaciones de Segundo Grado')
                                ->required()
                                ->columnSpanFull(),

                            Textarea::make('detail_descripcion')
                                ->label('Descripción del Tema')
                                ->placeholder('Breve descripción del contenido del tema...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),

                    // ── PASO 3: EXAMEN Y PREGUNTAS ────────────────────────
                    Step::make('Examen')
                        ->description('Define las preguntas y configuración del test.')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Textarea::make('titulo')
                                        ->label('Título del Examen')
                                        ->placeholder('Ej: Examen Parcial I')
                                        ->required(),
                                    Select::make('estado')
                                        ->options([
                                            'activo'   => 'Activo',
                                            'borrador' => 'Borrador',
                                        ])
                                        ->default('borrador'),
                                ]),

                            Grid::make(3)
                                ->schema([
                                    TextInput::make('duracion_minutos')
                                        ->label('Duración (min)')
                                        ->numeric()
                                        ->default(60),
                                    TextInput::make('intentos_maximos')
                                        ->label('Intentos Máximos')
                                        ->numeric()
                                        ->default(1),
                                    TextInput::make('puntaje_minimo')
                                        ->label('Nota Aprobatoria')
                                        ->numeric()
                                        ->default(10.5),
                                ]),

                            Repeater::make('questions')
                                ->relationship('questions')
                                ->label('Banco de Preguntas')
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
                                        ->label('¿Usar imagen?')
                                        ->live()
                                        ->afterStateHydrated(function (Checkbox $component, Get $get) {
                                            if ($get('imagen_path')) {
                                                $component->state(true);
                                            }
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
                                        ->relationship('options')
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
                                                            if ($get('imagen_path')) {
                                                                $component->state(true);
                                                            }
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
                    ->columnSpanFull(),
            ]);
    }
}