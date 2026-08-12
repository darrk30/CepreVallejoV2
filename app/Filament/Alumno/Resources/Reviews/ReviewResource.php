<?php

namespace App\Filament\Alumno\Resources\Reviews;

use App\Filament\Alumno\Resources\Reviews\Pages\CreateReview;
use App\Filament\Alumno\Resources\Reviews\Pages\EditReview;
use App\Filament\Alumno\Resources\Reviews\Pages\ListReviews;
use App\Filament\Alumno\Resources\Reviews\Schemas\ReviewForm;
use App\Filament\Alumno\Resources\Reviews\Tables\ReviewsTable;
use App\Models\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $modelLabel = 'Comentario';
    protected static ?string $pluralModelLabel = 'Comentarios';
    protected static ?string $navigationLabel = 'Comentarios';

    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
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
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
