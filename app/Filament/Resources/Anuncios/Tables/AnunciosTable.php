<?php

namespace App\Filament\Resources\Anuncios\Tables;

use App\Models\Anuncio;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnunciosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Banner')
                    ->square(),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'alerta' => 'danger',
                        'informativo' => 'info',
                        'promocion' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                    }),

                TextColumn::make('vigencia')
                    ->label('Vigencia')
                    ->getStateUsing(function (Anuncio $record): string {
                        $now = now();
                        if ($record->fecha_fin instanceof Carbon && $record->fecha_fin->isPast()) {
                            return 'Expirado';
                        }
                        if ($record->fecha_inicio instanceof Carbon && $record->fecha_inicio->isFuture()) {
                            return 'Programado';
                        }
                        return 'En curso';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Expirado' => 'danger',
                        'Programado' => 'warning',
                        'En curso' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'informativo' => 'Informativo',
                        'alerta' => 'Alerta',
                        'promocion' => 'Promoción',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ]),
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
