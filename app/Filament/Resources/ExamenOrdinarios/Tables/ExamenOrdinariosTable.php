<?php

namespace App\Filament\Resources\ExamenOrdinarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExamenOrdinariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título del Examen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // Texto en negrita para resaltar

                TextColumn::make('duracion_minutos')
                    ->label('Duración')
                    ->sortable()
                    ->suffix(' min')
                    ->badge()
                    ->color('gray'),

                // Contador dinámico de la relación respuestasCorrectas
                TextColumn::make('respuestas_correctas_count')
                    ->label('Preguntas (Claves)')
                    ->counts('respuestasCorrectas')
                    ->badge()
                    ->color(fn ($state): string => $state >= 120 ? 'success' : 'warning')
                    ->tooltip('Preguntas registradas. Deberían ser 120.'),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('creador.name')
                    ->label('Creado por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto para no saturar

                TextColumn::make('created_at')
                    ->label('Fecha Reg.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->options([
                        'activo' => 'Activos',
                        'inactivo' => 'Inactivos',
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
