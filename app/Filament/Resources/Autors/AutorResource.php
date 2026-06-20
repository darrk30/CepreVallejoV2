<?php

namespace App\Filament\Resources\Autors;

use App\Filament\Resources\Autors\Pages\CreateAutor;
use App\Filament\Resources\Autors\Pages\EditAutor;
use App\Filament\Resources\Autors\Pages\ListAutors;
use App\Filament\Resources\Autors\Schemas\AutorForm;
use App\Filament\Resources\Autors\Tables\AutorsTable;
use App\Models\Autor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AutorResource extends Resource
{
    protected static ?string $model = Autor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $recordTitleAttribute = 'Autor';

    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return AutorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutorsTable::configure($table);
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
            'index' => ListAutors::route('/'),
            'create' => CreateAutor::route('/create'),
            'edit' => EditAutor::route('/{record}/edit'),
        ];
    }
}
