<?php

namespace App\Filament\Resources\Podcasts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PodcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            // 2. Asegura que la tabla cargue respetando ese orden por defecto
            ->defaultSort('orden')
            ->columns([
                // Agregué algunas columnas básicas para que la tabla no se vea vacía
                ImageColumn::make('imagen_portada')
                    ->label('Portada')
                    ->square(),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable(),

                TextColumn::make('duracion_minutos')
                    ->label('Duración')
                    ->suffix(' min'),

                ToggleColumn::make('estado')
                    ->label('Activo')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
