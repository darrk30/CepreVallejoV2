<?php

namespace App\Filament\Resources\Inscriptions;

use App\Filament\Resources\Inscriptions\Pages\CreateInscription;
use App\Filament\Resources\Inscriptions\Pages\EditInscription;
use App\Filament\Resources\Inscriptions\Pages\ListInscriptions;
use App\Filament\Resources\Inscriptions\Schemas\InscriptionForm;
use App\Filament\Resources\Inscriptions\Tables\InscriptionsTable;
use App\Models\Inscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InscriptionResource extends Resource
{
    protected static ?string $model = Inscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Matrícula';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Matrícula';

    protected static ?string $pluralModelLabel = 'Matrícula';

    protected static ?string $modelLabel = 'Matrícula';
    
    protected static ?string $recordTitleAttribute = 'Matrícula';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return InscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InscriptionsTable::configure($table);
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
            'index' => ListInscriptions::route('/'),
            'create' => CreateInscription::route('/create'),
            'edit' => EditInscription::route('/{record}/edit'),
        ];
    }
}
