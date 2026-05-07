<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section; // Asegúrate de usar el namespace correcto de tu arquitectura
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Imágenes del Banner')
                    ->description('Sube las imágenes del banner. Se recomienda mantener una misma proporción.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('imagen_desktop_path')
                            ->label('Imagen Desktop (PC)')
                            ->image()
                            ->directory('banners/desktop')
                            ->required()
                            ->imageEditor()
                            ->columnSpan(1),

                        FileUpload::make('imagen_mobile_path')
                            ->label('Imagen Mobile (Celular) - Opcional')
                            ->image()
                            ->directory('banners/mobile')
                            ->imageEditor()
                            ->columnSpan(1),
                    ])->columnSpanFull(),

                Section::make('Configuración')
                    ->columns(2)
                    ->schema([
                        TextInput::make('enlace')
                            ->label('URL de Destino (Link)')
                            ->url()
                            ->placeholder('https://...')
                            ->prefixIcon('heroicon-m-link')
                            ->columnSpanFull(),

                        Select::make('tipo')
                            ->label('Visibilidad (Tipo)')
                            ->options([
                                'publico' => 'Público (Landing Page)',
                                'interno' => 'Interno (Campus / Alumnos)',
                            ])
                            ->default('publico')
                            ->required()
                            ->prefixIcon('heroicon-m-eye'),

                        Select::make('estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                            ])
                            ->default('Activo')
                            ->required()
                            ->prefixIcon('heroicon-m-check-circle'),
                    ])->columnSpanFull(),
            ]);
    }
}
