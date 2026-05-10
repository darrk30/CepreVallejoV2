<?php

namespace App\Filament\Resources\Inscriptions\Schemas;

use App\Models\AcademicCycle;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class InscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registro de Inscripción')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_id')
                                    ->label('Estudiante')
                                    ->relationship('student', 'dni', fn(Builder $query) => $query->with('user'))
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->dni} | {$record->apellidos}, {$record->user->name}")
                                    ->searchable(['dni', 'apellidos', 'user.name'])
                                    ->preload()
                                    ->required()
                                    // Esto habilita el botón "+" al lado del buscador
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nombres')
                                                    ->placeholder('Ej. Pepe')
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->prefixIcon('heroicon-m-user'), // Cambiado de icon() a prefixIcon()

                                                TextInput::make('apellidos')
                                                    ->label('Apellidos')
                                                    ->placeholder('Ej. Pérez')
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->prefixIcon('heroicon-m-identification'),

                                                TextInput::make('dni')
                                                    ->label('Documento (DNI)')
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->numeric()
                                                    ->length(8)
                                                    ->unique('students', 'dni')
                                                    ->prefixIcon('heroicon-m-finger-print'),

                                                TextInput::make('telefono')
                                                    ->label('Nro. de Teléfono')
                                                    ->placeholder('999 999 999')
                                                    ->prefix('+51')
                                                    ->length(9)
                                                    ->prefixIcon('heroicon-m-phone')
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->numeric()
                                                    ->tel()
                                                    ->unique('students', 'telefono'),

                                                TextInput::make('direccion')
                                                    ->label('Dirección')
                                                    ->placeholder('Av. Principal')
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->prefixIcon('heroicon-m-envelope')
                                                    ->columnSpanFull(),

                                                TextInput::make('email')
                                                    ->label('Correo Electrónico')
                                                    ->placeholder('correo@ejemplo.com')
                                                    ->email()
                                                    ->required()
                                                    ->extraInputAttributes(['required' => false])
                                                    ->unique('users', 'email')
                                                    ->prefixIcon('heroicon-m-envelope')
                                                    ->columnSpanFull(),
                                            ])
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        // Lógica para crear User y Student al mismo tiempo
                                        $user = User::create([
                                            'name' => $data['name'],
                                            'email' => $data['email'],
                                            'password' => bcrypt($data['dni']),
                                        ]);

                                        $user->assignRole('alumno');

                                        $student = Student::create([
                                            'user_id' => $user->id,
                                            'dni' => $data['dni'],
                                            'telefono' => $data['telefono'],
                                            'apellidos' => $data['apellidos'],
                                        ]);

                                        return $student->id;
                                    }),

                                Select::make('academic_cycle_id')
                                    ->label('Ciclo Académico')
                                    ->relationship('academicCycle', 'nombre', fn($query) => $query->where('estado', true))
                                    ->live()
                                    ->unique(
                                        table: 'inscriptions', // Asegúrate de que este sea el nombre real de tu tabla en BD
                                        column: 'academic_cycle_id',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function (Unique $rule, Get $get) {
                                            return $rule->where('student_id', $get('student_id'));
                                        }
                                    )
                                    ->validationMessages([
                                        'unique' => 'El estudiante seleccionado ya se encuentra inscrito en este ciclo académico.',
                                    ])
                                    // SOLUCIÓN: Cargar el total al editar (Hydration)
                                    ->afterStateHydrated(function (Set $set, $state) {
                                        if ($state) {
                                            $ciclo = AcademicCycle::find($state);
                                            $set('monto_total_ciclo', $ciclo?->precio ?? 0);
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        if (!$state) return;
                                        $ciclo = AcademicCycle::find($state);
                                        if ($ciclo) {
                                            $set('monto_total_ciclo', $ciclo->precio);
                                            $set('payments', [[
                                                'monto' => $ciclo->precio,
                                                'fecha_pago' => now()->format('Y-m-d H:i'),
                                                'estado' => 'pendiente',
                                                'metodo_pago' => 'efectivo',
                                            ]]);
                                            self::updateFinancialTotals($get, $set);
                                        }
                                    })
                                    ->required(),

                                DateTimePicker::make('fecha_inscripcion')
                                    ->label('Fecha/Hora Registro')
                                    ->default(now())->required(),
                            ]),
                    ])->columnSpanFull(),

                Section::make('Programación de Pagos')
                    ->schema([
                        Repeater::make('payments')
                            ->label('Pagos')
                            ->table([
                                TableColumn::make('Fecha de pago'),
                                TableColumn::make('Monto'),
                                TableColumn::make('Metodo de pago'),
                                TableColumn::make('Estado'),
                            ])
                            ->compact()
                            ->relationship()
                            ->schema([
                                // Grid::make(4)
                                //     ->schema([
                                DateTimePicker::make('fecha_pago')
                                    ->native(false)
                                    ->displayFormat('d-m-Y H:i')
                                    ->seconds(false)
                                    ->default(now())
                                    ->required()
                                    ->live(),

                                TextInput::make('monto')
                                    ->numeric()->required()
                                    ->prefix('S/')
                                    ->live(onBlur: true)
                                    ->readOnly(fn(Get $get) => $get('estado') === 'pagado')
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::balanceAmounts($get, $set)),

                                Select::make('metodo_pago')
                                    ->native(false)
                                    ->options([
                                        'efectivo' => 'Efectivo',
                                        'yape' => 'Yape',
                                        'plin' => 'Plin',
                                        'transferencia' => 'Transferencia',
                                    ])->default('efectivo')->required()->live(),

                                Select::make('estado')
                                    ->native(false)
                                    ->options(['pendiente' => 'Pendiente', 'pagado' => 'Pagado'])
                                    ->default('pendiente')->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateFinancialTotals($get, $set)),
                                // ]),
                            ])
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state, $old) {
                                // Solo recalculamos si se agregó o quitó una fila
                                if (count($state ?? []) !== count($old ?? [])) {
                                    self::recalculateEqually($get, $set);
                                }
                            })
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Resumen')
                    ->columns(3)
                    ->schema([
                        TextInput::make('monto_total_ciclo')->label('Total Ciclo')->prefix('S/')->readOnly()->live(),
                        TextInput::make('monto_pagado')->label('Total Cobrado')->prefix('S/')->readOnly(),
                        TextInput::make('saldo')->label('Total Restante')->prefix('S/')->readOnly()
                            ->extraInputAttributes(['class' => 'text-danger-600 font-bold']),
                    ])->columnSpanFull(),
            ]);
    }

    protected static function balanceAmounts(Get $get, Set $set)
    {
        $payments = $get('../../payments') ?? [];
        $totalCiclo = (float) $get('../../monto_total_ciclo');

        // Identificar cuáles son pendientes (los únicos que podemos ajustar)
        $pendientes = collect($payments)->where('estado', 'pendiente');
        $pagadosSum = collect($payments)->where('estado', 'pagado')->sum('monto');

        if ($pendientes->count() > 1) {
            $lastIndex = collect($payments)->last(fn($p) => $p['estado'] === 'pendiente');
            // Buscamos la llave del último pendiente
            $lastKey = 0;
            foreach ($payments as $key => $p) {
                if ($p['estado'] === 'pendiente') $lastKey = $key;
            }

            $sumaOtrosPendientes = 0;
            foreach ($payments as $key => $payment) {
                if ($payment['estado'] === 'pendiente' && $key !== $lastKey) {
                    $sumaOtrosPendientes += (float) ($payment['monto'] ?? 0);
                }
            }

            $payments[$lastKey]['monto'] = max(0, round($totalCiclo - $pagadosSum - $sumaOtrosPendientes, 2));
            $set('../../payments', $payments);
        }

        self::updateFinancialTotals($get, $set);
    }

    protected static function recalculateEqually(Get $get, Set $set)
    {
        $payments = $get('payments') ?? [];
        $totalCiclo = (float) $get('monto_total_ciclo');

        $pagados = collect($payments)->where('estado', 'pagado');
        $pendientes = collect($payments)->where('estado', 'pendiente');

        $montoPagadoSum = $pagados->sum('monto');
        $saldoParaPendientes = max(0, $totalCiclo - $montoPagadoSum);

        if ($pendientes->count() > 0) {
            $montoBase = round($saldoParaPendientes / $pendientes->count(), 2);
            $countPendientes = $pendientes->count();
            $i = 0;

            foreach ($payments as $key => $item) {
                if ($item['estado'] === 'pendiente') {
                    $i++;
                    // El último pendiente absorbe el ajuste de decimales
                    $payments[$key]['monto'] = ($i === $countPendientes)
                        ? round($saldoParaPendientes - ($montoBase * ($countPendientes - 1)), 2)
                        : $montoBase;
                }
            }
            $set('payments', $payments);
        }

        self::updateFinancialTotals($get, $set);
    }

    protected static function updateFinancialTotals(Get $get, Set $set)
    {
        $payments = $get('payments') ?? $get('../../payments') ?? [];
        $totalCiclo = (float) ($get('monto_total_ciclo') ?? $get('../../monto_total_ciclo') ?? 0);

        $pagado = collect($payments)->where('estado', 'pagado')->sum(fn($p) => (float)($p['monto'] ?? 0));

        $set('monto_pagado', $pagado);
        $set('saldo', max(0, $totalCiclo - $pagado));
        $set('../../monto_pagado', $pagado);
        $set('../../saldo', max(0, $totalCiclo - $pagado));
    }
}
