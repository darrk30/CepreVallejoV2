<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Models\Role;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Nombre del Rol con un diseño de Badge
                TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                // Contador de permisos para ver qué tan "poderoso" es el rol
                TextColumn::make('permissions_count')
                    ->label('Permisos Asignados')
                    ->counts('permissions') // Requiere que la relación esté en el modelo
                    ->badge()
                    ->color('success')
                    ->sortable(),

                // Fecha de creación oculta por defecto para limpiar la UI
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Aquí podrías añadir filtros por guard_name si usas varios
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Role $record) {
                        // Impedimos que borren al Administrador desde la fila
                        if ($record->name === 'Administrador') {
                            throw new \Exception('El rol maestro no puede ser eliminado.');
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            // Filtramos para no borrar al Admin en acciones masivas
                            $records->each(function ($record) {
                                if ($record->name !== 'Administrador') {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ]);
    }
}
