<?php

namespace App\Filament\Resources\Autors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AutorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Autor')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Autor')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('imagen')
                            ->label('Fotografía')
                            ->image()
                            ->directory('autores/fotos')
                            ->automaticallyResizeImagesMode('cover')
                            ->automaticallyCropImagesToAspectRatio('1:1')
                            ->automaticallyResizeImagesToWidth('300')
                            ->automaticallyResizeImagesToHeight('300'),

                        Toggle::make('estado')
                            ->label('Activo')
                            ->default(true)
                            ->required(),
                    ])
            ]);
    }
}
