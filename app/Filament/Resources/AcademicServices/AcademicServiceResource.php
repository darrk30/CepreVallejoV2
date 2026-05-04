<?php

namespace App\Filament\Resources\AcademicServices;

use App\Filament\Resources\AcademicServices\Pages\CreateAcademicService;
use App\Filament\Resources\AcademicServices\Pages\EditAcademicService;
use App\Filament\Resources\AcademicServices\Pages\ListAcademicServices;
use App\Filament\Resources\AcademicServices\Schemas\AcademicServiceForm;
use App\Filament\Resources\AcademicServices\Tables\AcademicServicesTable;
use App\Models\AcademicService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AcademicServiceResource extends Resource
{
    protected static ?string $model = AcademicService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Servicios';

    protected static ?string $pluralModelLabel = 'Servicios';

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $recordTitleAttribute = 'Servicio';

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return AcademicServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicServicesTable::configure($table);
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
            'index' => ListAcademicServices::route('/'),
            'create' => CreateAcademicService::route('/create'),
            'edit' => EditAcademicService::route('/{record}/edit'),
        ];
    }
}
