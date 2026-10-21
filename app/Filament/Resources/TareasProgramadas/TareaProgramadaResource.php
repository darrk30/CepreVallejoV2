<?php

namespace App\Filament\Resources\TareasProgramadas;

use App\Filament\Resources\TareasProgramadas\Pages\CreateTareaProgramada;
use App\Filament\Resources\TareasProgramadas\Pages\EditTareaProgramada;
use App\Filament\Resources\TareasProgramadas\Pages\ListTareasProgramadas;
use App\Filament\Resources\TareasProgramadas\Schemas\TareaProgramadaForm;
use App\Filament\Resources\TareasProgramadas\Tables\TareasProgramadasTable;
use App\Models\TareaProgramada;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TareaProgramadaResource extends Resource
{
    protected static ?string $model = TareaProgramada::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Tareas Programadas';

    protected static ?string $modelLabel = 'Tarea Programada';

    protected static ?string $pluralModelLabel = 'Tareas Programadas';

    protected static ?int $navigationSort = 28;

    public static function form(Schema $schema): Schema
    {
        return TareaProgramadaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TareasProgramadasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTareasProgramadas::route('/'),
            'create' => CreateTareaProgramada::route('/create'),
            'edit' => EditTareaProgramada::route('/{record}/edit'),
        ];
    }
}
