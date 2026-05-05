<?php

namespace App\Filament\Resources\Videos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Miniatura')
                    ->circular(),

                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('titulo')
                    ->label('Título del Video')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('url')
                    ->label('Enlace')
                    ->icon('heroicon-m-play-circle')
                    ->color('primary')
                    ->copyable() // Útil para copiar el link rápido
                    ->limit(30),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->groups([
                Group::make('area.nombre')
                    ->label('Área')
                    ->collapsible(),
            ])
            ->defaultGroup('area.nombre')
            ->filters([
                SelectFilter::make('area_id')
                    ->label('Área Académica')
                    ->relationship('area', 'nombre'),

                SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
