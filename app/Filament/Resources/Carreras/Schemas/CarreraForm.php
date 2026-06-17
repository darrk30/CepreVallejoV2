<?php

namespace App\Filament\Resources\Carreras\Schemas;

use App\Enums\AreaAcademica;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CarreraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('area')
                    ->label('Área Académica')
                    ->options(
                        collect(AreaAcademica::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->value])
                            ->toArray()
                    )
                    ->required()
                    ->native(false)
                    ->columnSpan(1),

                TextInput::make('nombre')
                    ->label('Nombre de la Carrera')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Ingeniería Civil')
                    ->columnSpan(1),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo')
                    ->required()
                    ->native(false)
                    ->columnSpan(2),
            ]);
    }
}
