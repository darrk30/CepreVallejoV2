<?php

namespace App\Filament\Resources\TareasProgramadas\Tables;

use App\Enums\ResultadoTarea;
use App\Models\TareaProgramada;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class TareasProgramadasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn(TareaProgramada $record) => $record->descripcion),

                TextColumn::make('comando')
                    ->label('Comando')
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),

                TextColumn::make('frecuencia_legible')
                    ->label('Frecuencia')
                    ->state(fn(TareaProgramada $record) => $record->descripcionFrecuencia()),

                ToggleColumn::make('activo')
                    ->label('Activa'),

                TextColumn::make('ultima_ejecucion_at')
                    ->label('Última ejecución')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->placeholder('Nunca'),

                TextColumn::make('ultimo_resultado')
                    ->label('Resultado')
                    ->badge()
                    ->formatStateUsing(fn(?ResultadoTarea $state) => $state?->label() ?? 'Sin ejecutar')
                    ->color(fn(?ResultadoTarea $state) => $state?->color() ?? 'gray'),
            ])
            ->recordActions([
                Action::make('ejecutar')
                    ->label('Ejecutar ahora')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn() => Auth::user()?->can('run_tarea_programada'))
                    ->requiresConfirmation()
                    ->modalDescription('Se ejecutará el comando de inmediato, fuera de su horario programado. ¿Continuar?')
                    ->action(function (TareaProgramada $record) {
                        if (! $record->comandoEsValido()) {
                            Notification::make()
                                ->title('Comando no permitido')
                                ->body('Este comando no está en la lista de comandos autorizados.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $exitCode = Artisan::call($record->comando);
                        $salida = Artisan::output();

                        $record->update([
                            'ultima_ejecucion_at' => now(),
                            'ultimo_resultado' => $exitCode === 0 ? ResultadoTarea::EXITO->value : ResultadoTarea::ERROR->value,
                            'ultima_salida' => $salida,
                        ]);

                        Notification::make()
                            ->title($exitCode === 0 ? 'Tarea ejecutada correctamente' : 'La tarea terminó con error')
                            ->body(\Illuminate\Support\Str::limit($salida ?: 'Sin salida.', 300))
                            ->color($exitCode === 0 ? 'success' : 'danger')
                            ->send();
                    }),

                Action::make('ver_salida')
                    ->label('Ver salida')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading('Última salida del comando')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn(TareaProgramada $record) => view('filament.pages.parts.tarea-salida', [
                        'salida' => $record->ultima_salida,
                    ]))
                    ->visible(fn(TareaProgramada $record) => filled($record->ultima_salida)),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
