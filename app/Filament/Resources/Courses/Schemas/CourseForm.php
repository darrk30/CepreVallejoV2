<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Course;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Detalles principales y visuales del curso.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre del Curso')
                                    ->required()
                                    ->maxLength(255)
                                    // Quitamos el afterStateUpdated para que no genere slugs
                                    ->prefixIcon('heroicon-m-book-open')
                                    ->extraInputAttributes(['required' => false]),

                                TextInput::make('codigo')
                                    ->label('Código Identificador')
                                    ->required()
                                    ->default(function () {
                                        $nextId = (Course::withTrashed()->max('id') ?? 0) + 1;
                                        return "CURS-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                                    })
                                    ->unique('courses', 'codigo', ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-hashtag')
                                    ->extraInputAttributes(['required' => false]),

                                Select::make('area_id')
                                    ->label('Área Académica')
                                    ->relationship('area', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-m-squares-2x2')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre del Área')
                                            ->required()
                                            ->extraInputAttributes(['required' => false]),
                                    ])
                                    ->columnSpanFull(),

                                Textarea::make('descripcion')
                                    ->label('Descripción del Curso')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Grid::make(1)
                    ->schema([
                        Section::make('Multimedia y Estado')
                            ->schema([
                                FileUpload::make('imagen_path')
                                    ->label('Imagen de Portada')
                                    ->image()
                                    ->imageEditor()
                                    ->optimize('webp', 80)
                                    ->maxImageWidth(1200)
                                    ->directory('courses-images')
                                    ->imageEditor()
                                    ->required(),

                                TextInput::make('horas_semanales')
                                    ->label('Horas por Semana')
                                    ->numeric()
                                    ->default(2)
                                    ->prefixIcon('heroicon-m-clock')
                                    ->extraInputAttributes(['required' => false]),

                                Select::make('estado')
                                    ->label('Estado Operativo')
                                    ->options([
                                        'Activo' => 'Activo',
                                        'Inactivo' => 'Inactivo',
                                        'Archivado' => 'Archivado',
                                    ])
                                    ->default('Activo')
                                    ->native(false)
                                    ->required()
                                    ->prefixIcon('heroicon-m-check-circle'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
