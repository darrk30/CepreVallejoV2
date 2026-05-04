<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Ordenamiento: Activos primero, luego por fecha de creación descendente
            ->modifyQueryUsing(fn (Builder $query) => 
                $query->orderBy('estado', 'desc')
                      ->orderBy('created_at', 'desc')
            )
            ->columns([
                // Datos del Usuario (Relación)
                TextColumn::make('user.name')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),

                // Datos del Estudiante
                TextColumn::make('apellidos')
                    ->label('Apellidos')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('user.email')
                    ->label('Correo Electrónico')
                    ->searchable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                // Estado visual
                IconColumn::make('estado')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                // Auditoría (Opcional, se puede mostrar/ocultar)
                TextColumn::make('user_create.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtros de tabla
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