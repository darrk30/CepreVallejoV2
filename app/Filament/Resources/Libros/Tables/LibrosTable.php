<?php

namespace App\Filament\Resources\Libros\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class LibrosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Portada')
                    ->circular(),

                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('autor')
                    ->label('Autor')
                    ->searchable()
                    ->toggleable(),

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

                TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable(),
            ])
            # Función para arrastrar y soltar (reordenar)
            ->reorderable('orden')
            # Configuración de agrupamiento por Área
            ->groups([
                Group::make('area.nombre')
                    ->label('Área')
                    ->collapsible(), # Permite contraer las secciones
            ])
            # Grupo por defecto al cargar la tabla
            ->defaultGroup('area.nombre')
            # Orden predeterminado
            ->defaultSort('orden', 'asc')
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
