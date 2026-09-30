<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Models\Banner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section; // Asegúrate de usar el namespace correcto de tu arquitectura
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Imágenes del Banner')
                    ->description('Sube las imágenes del banner. Se recomienda mantener una misma proporción. No es necesario si el enlace es un video de TikTok: se usa su miniatura automáticamente.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('imagen_desktop_path')
                            ->label('Imagen Desktop (PC)')
                            ->image()
                            ->directory('banners/desktop')
                            ->required(fn (Get $get) => ! Banner::tiktokVideoId($get('enlace')))
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
                            ->placeholder('https://... o un link de TikTok (tiktok.com/@usuario/video/...)')
                            ->helperText('Si pegas un link de un video de TikTok, su miniatura se descarga sola al guardar (no hace falta subirla a mano).')
                            ->live(onBlur: true)
                            ->prefixIcon('heroicon-m-link')
                            ->columnSpanFull(),

                        Select::make('tipo')
                            ->label('Visibilidad (Tipo)')
                            ->options([
                                'publico' => 'Banner principal',
                                'interno' => 'Interno (Campus / Alumnos)',
                                'informacion_publica' => 'Información Pública',
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
