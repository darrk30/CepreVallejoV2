<?php

namespace App\Filament\Alumno\Resources\Reviews\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                Hidden::make('fecha_hora')
                    ->default(now()),

                Hidden::make('estado')
                    ->default('activo'),

                
                RichEditor::make('mensaje')
                    ->label('Tu comentario')
                    ->required()
                    ->placeholder('Escribe tu comentario aquí...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                    ])
                    ->columnSpanFull(),
            ]);
    }
}