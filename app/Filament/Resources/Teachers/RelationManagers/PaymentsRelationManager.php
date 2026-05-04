<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    // protected static ?string $relatedResource = TeacherResource::class;

    protected static ?string $title = 'Historial de Pagos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('academic_cycle_id')
                    ->label('Ciclo Académico (Opcional)')
                    ->relationship('academicCycle', 'nombre')
                    ->searchable()
                    ->preload(),

                TextInput::make('monto')
                    ->label('Monto (S/.)')
                    ->numeric()
                    ->prefix('S/')
                    ->required(),

                DatePicker::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->default(now())
                    ->required(),

                Select::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'Transferencia BCP' => 'Transferencia BCP',
                        'Transferencia BBVA' => 'Transferencia BBVA',
                        'Transferencia Interbank' => 'Transferencia Interbank',
                        'Yape' => 'Yape',
                        'Plin' => 'Plin',
                        'Efectivo' => 'Efectivo',
                    ])
                    ->searchable()
                    ->required(),

                TextInput::make('numero_operacion')
                    ->label('N° de Operación / Referencia')
                    ->maxLength(255),

                Select::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Completado' => 'Completado',
                        'Anulado' => 'Anulado',
                    ])
                    ->default('Completado')
                    ->required(),

                FileUpload::make('comprobante_path')
                    ->label('Comprobante (Voucher o RxH)')
                    ->directory('pagos-docentes')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),

                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('fecha_pago')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('academicCycle.nombre')
                    ->label('Ciclo')
                    ->placeholder('General (Sin ciclo asignado)'),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->prefix('S/ ')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('metodo_pago')
                    ->label('Método'),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Completado' => 'success',
                        'Anulado' => 'danger',
                    }),
            ])
            ->filters([
                // Aquí podrías agregar un filtro por estado o fechas más adelante si la lista crece mucho
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Registrar Nuevo Pago')
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_create_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_pago', 'desc');
    }
}
