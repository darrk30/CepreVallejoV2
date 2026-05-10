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

                Select::make('panel')
                    ->label('¿Dónde mostrar este anuncio?')
                    ->options([
                        'todos' => '✨ Mostrar en Todos',
                        'alumno' => '🎓 Panel de Alumnos',
                        'docente' => '👨‍🏫 Panel de Docentes',
                        'publico' => '🌐 Web Principal (Público)',
                    ])
                    ->default('todos')
                    ->required()
                    ->native(false)
                    ->prefixIcon('heroicon-m-tv') // Le da un toque visual elegante
                    ->helperText('Selecciona el panel específico donde se publicará este anuncio.'),

                RichEditor::make('contenido')
                    ->label('Cuerpo del Anuncio')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->label('Banner / Imagen')
                    ->image()
                    ->directory('anuncios/banners')
                    ->imageEditor()
                    ->optimize('webp', 80)
                    ->maxImageWidth(1200),

                TextInput::make('url')
                    ->label('Enlace de Acción (Opcional)')
                    ->url()
                    ->placeholder('https://...'),

                DateTimePicker::make('fecha_inicio')
                    ->label('Inicia el')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->placeholder('Inmediatamente')
                    // Esto coloca la fecha y hora actual al abrir el formulario de creación
                    ->default(now()),

                DateTimePicker::make('fecha_fin')
                    ->label('Termina el')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->after('fecha_inicio')
                    ->placeholder('Nunca (Siempre visible)')
                    // No ponemos ->default(), así se mantiene vacío (null) por defecto
                    ->nullable(),

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
