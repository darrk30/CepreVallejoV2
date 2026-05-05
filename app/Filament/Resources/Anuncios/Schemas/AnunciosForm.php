<?php

namespace App\Filament\Resources\Anuncios\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnunciosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título del Anuncio')
                    ->required()
                    ->maxLength(255),

                Select::make('tipo')
                    ->label('Tipo de Comunicado')
                    ->options([
                        'informativo' => 'Informativo',
                        'alerta' => 'Alerta / Urgente',
                        'promocion' => 'Promoción',
                    ])
                    ->default('informativo')
                    ->required()
                    ->native(false),

                RichEditor::make('contenido')
                    ->label('Cuerpo del Anuncio')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->label('Banner / Imagen')
                    ->image()
                    ->directory('anuncios/banners')
                    ->imageEditor(),

                TextInput::make('url')
                    ->label('Enlace de Acción (Opcional)')
                    ->url()
                    ->placeholder('https://...'),

                DateTimePicker::make('fecha_inicio')
                    ->label('Inicia el')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->placeholder('Inmediatamente'),

                DateTimePicker::make('fecha_fin')
                    ->label('Termina el')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->after('fecha_inicio') // Validación lógica
                    ->placeholder('Nunca (Siempre visible)'),

                Select::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo')
                    ->required(),
            ]);
    }
}
