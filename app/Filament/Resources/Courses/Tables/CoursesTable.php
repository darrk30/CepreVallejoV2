<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Imagen de portada del curso
                ImageColumn::make('imagen_path')
                    ->label('Imagen')
                    ->circular() // Diseño moderno circular
                    ->disk('public'), // Asegúrate de que el disk coincida con tu config

                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->color('gray'),

                TextColumn::make('nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Course $record): ?string => $record->descripcion) // Muestra descripción debajo
                    ->wrap(),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('horas_semanales')
                    ->label('Hrs/Sem')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                // Gestión de estados con lógica de colores de Tukipu
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)) // Asegura Primera Mayúscula
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'activo' => 'success',
                        'inactivo' => 'warning',
                        'archivado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('creator.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('area_id')
                    ->label('Filtrar por Área')
                    ->relationship('area', 'nombre'),

                SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        'archivado' => 'Archivado',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
