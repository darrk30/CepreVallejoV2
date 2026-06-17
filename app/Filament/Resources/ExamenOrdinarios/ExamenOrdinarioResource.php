<?php

namespace App\Filament\Resources\ExamenOrdinarios;

use App\Filament\Resources\ExamenOrdinarios\Pages\CreateExamenOrdinario;
use App\Filament\Resources\ExamenOrdinarios\Pages\EditExamenOrdinario;
use App\Filament\Resources\ExamenOrdinarios\Pages\ListExamenOrdinarios;
use App\Filament\Resources\ExamenOrdinarios\Schemas\ExamenOrdinarioForm;
use App\Filament\Resources\ExamenOrdinarios\Tables\ExamenOrdinariosTable;
use App\Models\ExamenOrdinario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamenOrdinarioResource extends Resource
{
    protected static ?string $model = ExamenOrdinario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ExamenOrdinario';

    public static function form(Schema $schema): Schema
    {
        return ExamenOrdinarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamenOrdinariosTable::configure($table);
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
            'index' => ListExamenOrdinarios::route('/'),
            'create' => CreateExamenOrdinario::route('/create'),
            'edit' => EditExamenOrdinario::route('/{record}/edit'),
        ];
    }
}
