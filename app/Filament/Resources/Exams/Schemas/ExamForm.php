<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Models\CicloCourseTeacher;
use App\Models\TeacherCourseContent;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // PASO 1: CREACIÓN DEL CONTENIDO
                    Step::make('Contenido del Profesor')
                        ->description('Asigna el curso y crea la lección para el examen.')
                        ->schema([
                            Select::make('ciclo_course_teacher_id')
                                ->label('Asignación: Ciclo - Curso - Profesor')
                                ->options(function () {
                                    // Cargamos las relaciones necesarias: 
                                    // Docente -> Usuario (para el nombre)
                                    // Pivote CicloCourse -> Curso
                                    // Pivote CicloCourse -> Ciclo Académico
                                    return CicloCourseTeacher::with([
                                        'teacher.user',
                                        'cicloCourse.course',
                                        'cicloCourse.academicCycle'
                                    ])
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            // Accedemos a los datos a través de la cadena de relaciones
                                            $ciclo = $item->cicloCourse->academicCycle->nombre ?? 'Ciclo S/N';
                                            $curso = $item->cicloCourse->course->nombre ?? 'Curso S/N';
                                            $profe = $item->teacher->user->name ?? 'Profesor S/N';

                                            // Formato solicitado: CICLO - CURSO - TEACHER
                                            return [$item->id => "{$ciclo} - {$curso} - {$profe}"];
                                        });
                                })
                                ->searchable()
                                ->required()
                                ->columnSpanFull(),

                            TextInput::make('content_titulo')
                                ->label('Título del Tema / Lección')
                                ->placeholder('Ej: Semana 04: Ecuaciones de Segundo Grado')
                                ->required(),

                            Textarea::make('content_descripcion')
                                ->label('Descripción del Contenido')
                                ->rows(2),
                        ]),

                    // PASO 2: CREACIÓN DEL EXAMEN Y PREGUNTAS


                    Step::make('Detalles del Examen')
                        ->description('Define las preguntas y configuración del test.')
                        ->schema([
                            // Configuración General del Examen
                            Grid::make(2)
                                ->schema([
                                    Textarea::make('titulo')
                                        ->label('Título del Examen')
                                        ->placeholder('Ej: Examen Parcial I')
                                        ->required(),
                                    Select::make('estado')
                                        ->options([
                                            'activo' => 'Activo',
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

                            // Banco de Preguntas
                            Repeater::make('questions')
                                ->relationship('questions')
                                ->label('Banco de Preguntas')
                                ->collapsible()
                                ->cloneable()
                                ->itemLabel(fn(array $state): ?string => $state['texto_pregunta'] ?? 'Nueva Pregunta')
                                ->schema([
                                    // Fila: Pregunta y Puntos
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
                                            // Mantenemos la inteligencia: si hay imagen, se marca solo
                                            if ($get('imagen_path')) {
                                                $component->state(true);
                                            }
                                        })
                                        ->columnSpan(1),

                                    FileUpload::make('imagen_path')
                                        ->label('Imagen de apoyo')
                                        ->image()
                                        ->directory('exams/questions')
                                        ->imageEditor()
                                        ->optimize('webp', 80)
                                        ->maxImageWidth(1200)
                                        ->columnSpanFull()
                                        ->visible(fn(Get $get): bool => (bool) $get('config_show_image')),

                                    // Alternativas (Sub-Repeater)
                                    Repeater::make('options')
                                        ->relationship('options')
                                        ->label('Alternativas')
                                        ->grid(2) // Dos columnas para que sea compacto
                                        ->schema([
                                            Textarea::make('texto_opcion')
                                                ->label('Texto de la Opción')
                                                ->required(),

                                            Grid::make(2) // Mantiene la fila dividida en dos
                                                ->schema([
                                                    Checkbox::make('es_correcta')
                                                        ->label('¿Es la correcta?')
                                                        // Nota: Checkbox no usa onColor, usa el estilo por defecto de tu tema
                                                        ->columnSpan(1),

                                                    Checkbox::make('config_show_image_opt')
                                                        ->label('¿Usar imagen?')
                                                        ->live()
                                                        ->afterStateHydrated(function (Checkbox $component, Get $get) {
                                                            // Mantenemos la inteligencia: si hay imagen, se marca solo
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
                        ])
                ])
                    ->columnSpanFull()
                    ->submitAction(new \Illuminate\Support\HtmlString('<button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary">Guardar Contenido y Examen</button>')),
            ]);
    }
}
