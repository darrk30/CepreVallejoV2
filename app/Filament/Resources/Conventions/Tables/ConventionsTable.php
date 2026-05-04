<?php

namespace App\Filament\Resources\Conventions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ConventionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_path')
                    ->label('Logo')
                    ->square()
                    ->extraImgAttributes(['class' => 'cursor-pointer hover:scale-105 transition'])
                    ->action(
                        Action::make('preview')
                            ->modalHeading('Logo de la Institución')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Cerrar')
                            ->modalWidth('sm')
                            ->modalContent(fn($record) => new HtmlString('
                                <div style="display: flex; justify-content: center; padding: 20px; background-color: #f9fafb; border-radius: 8px;">
                                    <img src="' . Storage::url($record->imagen_path) . '" 
                                         style="max-width: 100%; max-height: 250px; object-fit: contain;" 
                                         alt="Logo" />
                                </div>
                            '))
                    )
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('nombre')
                    ->label('Institución')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('periodo')
                    ->label('Vigencia')
                    ->searchable(),

                TextColumn::make('estado_convenio')
                    ->label('Acuerdo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Vigente' => 'success',
                        'En Renovación' => 'warning',
                        'Finalizado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('estado')
                    ->label('Web')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Activo' => 'info',
                        'Inactivo' => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true), // Lo oculta por defecto para no saturar la tabla, pero se puede mostrar desde el menú de columnas
            ])
            ->filters([
                SelectFilter::make('estado_convenio')
                    ->label('Estado Acuerdo')
                    ->options([
                        'Vigente' => 'Vigentes',
                        'En Renovación' => 'En Renovación',
                        'Finalizado' => 'Finalizados',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
