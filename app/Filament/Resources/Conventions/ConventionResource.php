<?php

namespace App\Filament\Resources\Conventions;

use App\Filament\Resources\Conventions\Pages\CreateConvention;
use App\Filament\Resources\Conventions\Pages\EditConvention;
use App\Filament\Resources\Conventions\Pages\ListConventions;
use App\Filament\Resources\Conventions\Schemas\ConventionForm;
use App\Filament\Resources\Conventions\Tables\ConventionsTable;
use App\Models\Convention;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ConventionResource extends Resource
{
    protected static ?string $model = Convention::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

        // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Convenios';

    protected static ?string $pluralModelLabel = 'Convenios';

    protected static ?string $modelLabel = 'Convenio';

    protected static ?string $recordTitleAttribute = 'Convenio';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return ConventionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConventionsTable::configure($table);
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
            'index' => ListConventions::route('/'),
            'create' => CreateConvention::route('/create'),
            'edit' => EditConvention::route('/{record}/edit'),
        ];
    }
}
