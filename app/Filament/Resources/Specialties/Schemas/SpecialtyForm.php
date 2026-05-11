<?php

namespace App\Filament\Resources\Specialties\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SpecialtyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Especialidad')
                    ->description('Define el nombre y estado de la especialidad académica.')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la Especialidad')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ej. Matemática, Física, Comprensión Lectora')
                            ->columnSpanFull(),

                        Toggle::make('estado')
                            ->label('¿Especialidad Activa?')
                            ->default(true) // Por defecto marcada
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark'),
                    ])->columns(2),
            ]);
    }
}
