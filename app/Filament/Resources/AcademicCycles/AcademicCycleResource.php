<?php

namespace App\Filament\Resources\AcademicCycles;

use App\Filament\Resources\AcademicCycles\Pages\CreateAcademicCycle;
use App\Filament\Resources\AcademicCycles\Pages\EditAcademicCycle;
use App\Filament\Resources\AcademicCycles\Pages\ListAcademicCycles;
use App\Filament\Resources\AcademicCycles\RelationManagers\CoursesRelationManager;
use App\Filament\Resources\AcademicCycles\Schemas\AcademicCycleForm;
use App\Filament\Resources\AcademicCycles\Tables\AcademicCyclesTable;
use App\Models\AcademicCycle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AcademicCycleResource extends Resource
{
    protected static ?string $model = AcademicCycle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Ciclos';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Ciclos';

    protected static ?string $pluralModelLabel = 'Ciclos';

    protected static ?string $modelLabel = 'Ciclo';

    protected static ?string $recordTitleAttribute = 'Ciclo';

    protected static ?int $navigationSort = 5;


    public static function form(Schema $schema): Schema
    {
        return AcademicCycleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicCyclesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAcademicCycles::route('/'),
            'create' => CreateAcademicCycle::route('/create'),
            'edit' => EditAcademicCycle::route('/{record}/edit'),
        ];
    }
}
