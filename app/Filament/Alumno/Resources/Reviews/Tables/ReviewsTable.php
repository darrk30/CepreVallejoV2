<?php

namespace App\Filament\Alumno\Resources\Reviews\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mensaje')
                    ->label('Título')
                    ->formatStateUsing(fn (string $state): string => Str::limit(strip_tags($state), 50))
                    ->searchable(),

                TextColumn::make('fecha_hora')
                    ->label('Fecha de envío')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}