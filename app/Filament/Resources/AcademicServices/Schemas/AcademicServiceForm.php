<?php

namespace App\Filament\Resources\AcademicServices\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AcademicServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Servicio')
                    ->description('Completa los datos del servicio académico que se ofrecerá.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título del Servicio')
                            ->placeholder('Ej. Orientación Vocacional')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-academic-cap')
                            ->columnSpanFull(),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Explica brevemente en qué consiste este servicio...')
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('imagen_path')
                            ->label('Imagen Representativa')
                            ->image()
                            ->imageEditor()
                            ->optimize('webp', 80)
                            ->maxImageWidth(1200)
                            ->directory('servicios_academicos')
                            ->columnSpan(1),

                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                            ])
                            ->default('Activo')
                            ->required()
                            ->prefixIcon('heroicon-m-check-circle')
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
