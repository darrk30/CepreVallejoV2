<?php

namespace App\Filament\Resources\Anuncios;

use App\Filament\Resources\Anuncios\Pages\CreateAnuncios;
use App\Filament\Resources\Anuncios\Pages\EditAnuncios;
use App\Filament\Resources\Anuncios\Pages\ListAnuncios;
use App\Filament\Resources\Anuncios\Schemas\AnunciosForm;
use App\Filament\Resources\Anuncios\Tables\AnunciosTable;
use App\Models\Anuncio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnunciosResource extends Resource
{
    protected static ?string $model = Anuncio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Configuración';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Anuncios';

    protected static ?string $pluralModelLabel = 'Anuncios';

    protected static ?string $modelLabel = 'Anuncio';

    protected static ?string $recordTitleAttribute = 'Anuncio';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return AnunciosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnunciosTable::configure($table);
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
            'index' => ListAnuncios::route('/'),
            'create' => CreateAnuncios::route('/create'),
            'edit' => EditAnuncios::route('/{record}/edit'),
        ];
    }
}
