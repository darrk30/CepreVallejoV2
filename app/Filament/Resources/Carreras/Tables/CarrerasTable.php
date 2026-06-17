<?php

namespace App\Filament\Resources\Carreras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CarrerasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('area')
                    ->label('Área')
                    ->searchable()
                    ->sortable()
                    ->badge() // Le da un diseño de "etiqueta" visualmente atractivo
                    ->color('info'),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // SelectFilter::make('area')
                //     ->label('Filtrar por Área'),
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
