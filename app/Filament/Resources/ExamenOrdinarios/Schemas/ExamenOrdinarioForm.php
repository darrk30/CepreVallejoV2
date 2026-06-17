<?php

namespace App\Filament\Resources\ExamenOrdinarios\Schemas;

use App\Enums\AsignaturaExamen;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ExamenOrdinarioForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Examen Ordinario')
                    ->tabs([
                        Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('titulo')
                                    ->label('Título del Examen')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Examen Ordinario Ceprevallejo 2026 - I')
                                    ->columnSpanFull(),

                                FileUpload::make('pdf_path')
                                    ->label('Archivo del Examen (PDF)')
                                    ->required()
                                    ->visibility('public')
                                    ->directory('examenes-pdf')
                                    ->preserveFilenames()
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpan(2),

                                TextInput::make('duracion_minutos')
                                    ->label('Duración (en min)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Ej: 180')
                                    ->suffix('minutos')
                                    ->columnSpan(1),

                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'activo' => 'Activo',
                                        'inactivo' => 'Inactivo',
                                    ])
                                    ->default('activo')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),

                                // --- CAMPO EXCEL ACTUALIZADO ---
                                FileUpload::make('excel_claves')
                                    ->label('Importar claves (Excel/CSV)')
                                    ->helperText('Opcional. Puedes importar las claves masivamente. Si subes un archivo, NO debes llenar las respuestas manuales en la otra pestaña, ya que serán reemplazadas por las del Excel.')
                                    ->acceptedFileTypes([
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-excel',
                                        'text/csv'
                                    ])
                                    ->directory('temp-excel-claves')
                                    ->visibleOn('create')
                                    ->columnSpanFull(),
                            ])->columns(4),

                        Tab::make('Cartilla de Respuestas (Clave)')
                            ->icon('heroicon-o-squares-2x2')
                            // Se quitó el ->hidden(), ahora siempre se muestra para que puedan editar a mano o crear desde cero
                            ->schema([
                                Repeater::make('respuestasCorrectas')
                                    ->relationship('respuestasCorrectas')
                                    ->label('Preguntas del Examen')
                                    ->addActionLabel('Agregar Pregunta')
                                    ->grid(['default' => 2, 'sm' => 2, 'md' => 3, 'lg' => 4])
                                    ->defaultItems(0)
                                    ->cloneable()
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('numero_pregunta')
                                                    ->label('N° Pregunta')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->required()
                                                    ->distinct()
                                                    ->default(function (Get $get) {
                                                        $filas = $get('../../respuestasCorrectas');
                                                        if (empty($filas)) return 1;
                                                        $maximo = 0;
                                                        foreach ($filas as $fila) {
                                                            $valor = (int) ($fila['numero_pregunta'] ?? 0);
                                                            if ($valor > $maximo) $maximo = $valor;
                                                        }
                                                        return $maximo + 1;
                                                    }),

                                                Select::make('asignatura')
                                                    ->label('Asignatura')
                                                    ->options(
                                                        collect(AsignaturaExamen::cases())
                                                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                                                            ->toArray()
                                                    )
                                                    ->required()
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        $asignatura = AsignaturaExamen::tryFrom($state);
                                                        if ($asignatura) {
                                                            $set('bloque', $asignatura->bloque()->value);
                                                        }
                                                    }),
                                            ]),

                                        TextInput::make('bloque')
                                            ->label('Bloque')
                                            ->readOnly()
                                            ->helperText('Se asigna automáticamente según la asignatura'),

                                        ToggleButtons::make('opcion_correcta')
                                            ->label('Opción Correcta')
                                            ->required()
                                            ->options([
                                                'A' => 'A',
                                                'B' => 'B',
                                                'C' => 'C',
                                                'D' => 'D',
                                                'E' => 'E',
                                            ])
                                            ->colors([
                                                'A' => 'info',
                                                'B' => 'info',
                                                'C' => 'info',
                                                'D' => 'info',
                                                'E' => 'info',
                                            ])
                                            ->inline(),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
