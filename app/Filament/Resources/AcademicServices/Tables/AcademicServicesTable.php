<?php

namespace App\Filament\Resources\AcademicServices\Tables;

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

class AcademicServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_path')
                    ->label('Imagen')
                    ->width('150px')
                    ->extraImgAttributes(['class' => 'cursor-pointer hover:opacity-80 transition'])
                    ->action(
                        Action::make('preview')
                            ->modalHeading('Vista Previa del Servicio')
                            ->modalSubmitAction(false) 
                            ->modalCancelActionLabel('Cerrar')
                            ->modalContent(fn ($record) => new HtmlString('
                                <div style="display: flex; justify-content: center; padding: 20px;">
                                    <img src="' . Storage::url($record->imagen_path) . '" 
                                        style="max-width: 100%; max-height: 350px; width: auto; height: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" 
                                        alt="Preview" />
                                </div>
                            '))
                    )
                    ->defaultImageUrl(url('/images/placeholder.png')), // Opcional: si tienes una imagen por defecto

                TextColumn::make('titulo')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50) // Cortamos el texto para que no desborde la tabla
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        // Muestra el texto completo al pasar el mouse por encima
                        return strlen($state) > 50 ? $state : null;
                    })
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
                SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->options([
                        'Activo' => 'Activos',
                        'Inactivo' => 'Inactivos',
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
