<?php

namespace App\Filament\Resources\Specialties\Tables;

use DragonCode\Support\Helpers\Boolean;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SpecialtiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Especialidad')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('teachers_count')
                    ->label('Docentes')
                    ->counts('teachers')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                IconColumn::make('estado')
                    ->label('Estado')
                    ->boolean() // Detecta automáticamente true/false
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Última edición')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
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
