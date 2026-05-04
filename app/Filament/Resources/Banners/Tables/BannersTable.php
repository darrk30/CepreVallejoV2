<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_desktop_path')
                    ->label('Banner')
                    ->width('150px')
                    ->extraImgAttributes(['class' => 'cursor-pointer hover:opacity-80 transition'])
                    // Esta acción abre el modal de previsualización al hacer clic en la imagen
                    ->action(
                        Action::make('preview')
                            ->modalHeading('Vista Previa del Banner')
                            ->modalSubmitAction(false) // Ocultamos el botón de guardar
                            ->modalCancelActionLabel('Cerrar')
                            ->modalContent(fn ($record) => new HtmlString('
                                <div style="display: flex; justify-content: center; padding: 20px;">
                                    <img src="' . Storage::url($record->imagen_desktop_path) . '" 
                                        style="max-width: 100%; max-height: 350px; width: auto; height: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" 
                                        alt="Preview" />
                                </div>
                            '))
                    ),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'publico' => 'info',
                        'interno' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('enlace')
                    ->label('Enlace')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Inactivo' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'publico' => 'Público',
                        'interno' => 'Interno',
                    ]),
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
            ])
            ->reorderable('orden')
            ->defaultSort('orden');;
    }
}
