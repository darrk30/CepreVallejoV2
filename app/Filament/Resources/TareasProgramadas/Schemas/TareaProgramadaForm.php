<?php

namespace App\Filament\Resources\TareasProgramadas\Schemas;

use App\Enums\FrecuenciaTarea;
use App\Models\TareaProgramada;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TareaProgramadaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tarea programada')
                ->description('Solo se pueden programar comandos de una lista fija (por seguridad). Los cambios se aplican en la siguiente ejecución del scheduler.')
                ->schema([
                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),

                    Textarea::make('descripcion')
                        ->rows(2)
                        ->columnSpanFull(),

                    Select::make('comando')
                        ->label('Comando a ejecutar')
                        ->options(TareaProgramada::comandosDisponibles())
                        ->native(false)
                        ->required()
                        ->columnSpanFull(),

                    Toggle::make('activo')
                        ->label('Activa')
                        ->default(true)
                        ->helperText('Si está apagada, el scheduler la ignora aunque exista el registro.')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Frecuencia')
                ->schema([
                    Select::make('frecuencia')
                        ->label('¿Cada cuánto se ejecuta?')
                        ->options(collect(FrecuenciaTarea::cases())->mapWithKeys(fn($f) => [$f->value => $f->label()]))
                        ->default(FrecuenciaTarea::DIARIA->value)
                        ->native(false)
                        ->live()
                        ->required(),

                    TimePicker::make('hora')
                        ->label('Hora')
                        ->seconds(false)
                        ->default('00:05')
                        ->visible(fn(Get $get) => in_array($get('frecuencia'), [
                            FrecuenciaTarea::DIARIA->value,
                            FrecuenciaTarea::SEMANAL->value,
                            FrecuenciaTarea::MENSUAL->value,
                        ]))
                        ->required(fn(Get $get) => in_array($get('frecuencia'), [
                            FrecuenciaTarea::DIARIA->value,
                            FrecuenciaTarea::SEMANAL->value,
                            FrecuenciaTarea::MENSUAL->value,
                        ])),

                    Select::make('dia_semana')
                        ->label('Día de la semana')
                        ->options([
                            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado',
                        ])
                        ->native(false)
                        ->visible(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::SEMANAL->value)
                        ->required(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::SEMANAL->value),

                    TextInput::make('dia_mes')
                        ->label('Día del mes')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(31)
                        ->visible(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::MENSUAL->value)
                        ->required(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::MENSUAL->value),

                    TextInput::make('expresion_cron')
                        ->label('Expresión cron')
                        ->placeholder('5 0 * * *')
                        ->helperText('Formato: minuto hora día-mes mes día-semana')
                        ->visible(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::PERSONALIZADA->value)
                        ->required(fn(Get $get) => $get('frecuencia') === FrecuenciaTarea::PERSONALIZADA->value)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
