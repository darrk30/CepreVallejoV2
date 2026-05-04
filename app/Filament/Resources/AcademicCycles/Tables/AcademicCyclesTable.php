<?php

namespace App\Filament\Resources\AcademicCycles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AcademicCyclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Ordenamiento: Activos primero, luego los más recientes
            ->modifyQueryUsing(fn (Builder $query) => 
                $query->orderBy('estado', 'desc')
                      ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre del Ciclo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('año')
                    ->label('Año')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('numero')
                    ->label('N°')
                    ->alignCenter(),

                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                IconColumn::make('estado')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-play-circle')
                    ->falseIcon('heroicon-o-pause-circle'),

                // Auditoría: Quién lo creó
                TextColumn::make('user_create.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtros opcionales por año
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