<?php

namespace App\Filament\Resources\Conventions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // Columna Izquierda (Datos Principales - Ocupa 2 espacios)
                        Section::make('Detalles del Convenio')
                            ->columnSpan(2)
                            ->columns(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Institución / Empresa')
                                    ->placeholder('Ej. Universidad Nacional Mayor de San Marcos')
                                    ->required()
                                    ->prefixIcon('heroicon-m-building-library')
                                    ->columnSpanFull(),

                                Textarea::make('descripcion')
                                    ->label('Descripción del Convenio')
                                    ->placeholder('Beneficios, alcance del acuerdo...')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                TextInput::make('periodo')
                                    ->label('Periodo de Vigencia')
                                    ->placeholder('Ej. 2024 - 2026')
                                    ->prefixIcon('heroicon-m-calendar'),

                                TextInput::make('representante')
                                    ->label('Representante / Contacto')
                                    ->placeholder('Nombre de la contraparte')
                                    ->prefixIcon('heroicon-m-briefcase'),
                            ])->columnSpanFull(),

                        // Columna Derecha (Logo y Estados - Ocupa 1 espacio)
                        Section::make('Configuración')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('imagen_path')
                                    ->label('Logo de la Institución')
                                    ->image()
                                    ->directory('convenios/logos')
                                    ->imageEditor(),

                                Select::make('estado_convenio')
                                    ->label('Estado del Acuerdo')
                                    ->options([
                                        'Vigente' => 'Vigente',
                                        'En Renovación' => 'En Renovación',
                                        'Finalizado' => 'Finalizado',
                                    ])
                                    ->native(false)
                                    ->default('Vigente')
                                    ->required()
                                    ->prefixIcon('heroicon-m-document-check'),

                                Select::make('estado')
                                    ->label('Visibilidad (Web)')
                                    ->options([
                                        'Activo' => 'Activo (Visible)',
                                        'Inactivo' => 'Inactivo (Oculto)',
                                    ])
                                    ->native(false)
                                    ->default('Activo')
                                    ->required()
                                    ->prefixIcon('heroicon-m-eye'),
                            ])->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
