<?php

namespace App\Filament\Resources\Podcasts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PodcastForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Podcast')
                    ->description('Registra la información del podcast y el enlace de reproducción.')
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título del podcast')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('descripcion')
                            ->label('Descripción')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'bulletList', // <-- Cambiado de 'unorderedList' a 'bulletList'
                            ])
                            ->columnSpanFull(),
                        Select::make('autor_id')
                            ->label('Autor')
                            ->relationship('autor', 'nombre') // Relación definida en tu modelo Podcast
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([ // Esto abre un mini-formulario para crear el autor
                                TextInput::make('nombre')
                                    ->label('Nombre del Autor')
                                    ->required(),
                                FileUpload::make('imagen')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('autores/fotos'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return \App\Models\Autor::create($data)->id;
                            })
                            ->editOptionForm([ // Opcional: permite editar el autor seleccionado
                                TextInput::make('nombre')
                                    ->label('Nombre del Autor')
                                    ->required(),
                                FileUpload::make('imagen')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('autores/fotos'),
                            ]),
                        FileUpload::make('url_audio')
                            ->label('Archivo de Audio (MP3)')
                            ->disk('public') // el disco que configuraremos en filesystems.php
                            ->directory('podcasts/audios')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav'])
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('imagen_portada')
                            ->label('Imagen de Portada')
                            ->image()
                            ->directory('podcasts/portadas')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1') // Cuadrado estilo Spotify
                            ->imageResizeTargetWidth('500')
                            ->imageResizeTargetHeight('500'),

                        Grid::make(1)
                            ->schema([
                                TextInput::make('duracion_minutos')
                                    ->label('Duración (Minutos)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('Ej. 45'),

                                Toggle::make('estado')
                                    ->label('¿Está activo para reproducción?')
                                    ->default(true)
                                    ->required()
                                    ->inline(false),
                            ])->columnSpan(1),

                    ])->columnSpanFull()
            ]);
    }
}
