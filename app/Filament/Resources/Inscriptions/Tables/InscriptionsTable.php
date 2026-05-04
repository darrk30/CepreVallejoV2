<?php

namespace App\Filament\Resources\Inscriptions\Tables;

use App\Models\Inscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('student.user.name')
                    ->label('Estudiante')
                    ->description(fn(Inscription $record): string => $record->student->apellidos)
                    ->searchable(['dni', 'apellidos']),

                TextColumn::make('academicCycle.nombre')
                    ->label('Ciclo')
                    ->badge()
                    ->color('info'),

                // BADGE: PAGOS PAGADOS
                TextColumn::make('pagos_realizados_count')
                    ->label('Pagados')
                    ->state(fn(Inscription $record): int => $record->payments()->where('estado', 'pagado')->count())
                    ->badge()
                    ->color('success')
                    // ... dentro de la columna de Pagados o Pendientes
                    ->action(
                        Action::make('ver_pagados')
                            ->modalHeading('Detalle de Pagos Realizados') // Cambio: modalTitle -> modalHeading
                            ->modalSubmitAction(false) // Oculta el botón de procesar
                            ->modalCancelActionLabel('Cerrar')
                            ->modalContent(fn(Inscription $record) => view('filament.inscriptions.view-payments', [
                                'payments' => $record->payments()->where('estado', 'pagado')->get(),
                            ]))
                    ),

                // BADGE: PAGOS PENDIENTES
                TextColumn::make('pagos_pendientes_count')
                    ->label('Pendientes')
                    ->state(fn(Inscription $record): int => $record->payments()->where('estado', 'pendiente')->count())
                    ->badge()
                    ->color('danger')
                    ->action(
                        Action::make('ver_pendientes')
                            ->modalHeading('Cronograma de Pagos Pendientes')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Cerrar')
                            ->modalContent(fn(Inscription $record) => view('filament.inscriptions.view-payments', [
                                'payments' => $record->payments()->where('estado', 'pendiente')->get(),
                            ]))
                    ),

                TextColumn::make('monto_pagado')
                    ->label('Total')
                    ->prefix('S/ ')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total')),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->prefix('S/ ')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->weight(FontWeight::Bold),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('academic_cycle_id')
                    ->label('Ciclo')
                    ->relationship('academicCycle', 'nombre'),
                \Filament\Tables\Filters\SelectFilter::make('estado_pago')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'parcial' => 'Parcial',
                        'pagado' => 'Pagado',
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
