<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECCIÓN 1: IDENTIDAD (Nombres, Apellidos, DNI)
                Section::make('Identidad del Estudiante')
                    ->description('Información básica de identificación.')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('user.name')
                                    ->label('Nombres')
                                    ->required()
                                    ->placeholder('Ej. Juan'),

                                TextInput::make('apellidos')
                                    ->label('Apellidos')
                                    ->required()
                                    ->placeholder('Ej. Perez'),

                                TextInput::make('dni')
                                    ->label('DNI')
                                    ->required()
                                    ->numeric()
                                    ->minLength(8)
                                    ->maxLength(8)
                                    ->unique('students', 'dni', ignoreRecord: true),

                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->placeholder('987654321'),
                            ]),

                        TextInput::make('direccion')
                            ->label('Dirección de Domicilio')
                            ->placeholder('Ej. Av. Principal 123, Lima')
                            ->columnSpanFull(),

                        Toggle::make('estado')
                            ->label('Estado Activo')
                            ->default(true)
                            ->helperText('Define si el alumno puede acceder a los servicios académicos.')
                            ->inline(false),
                    ]),

                // SECCIÓN 2: ACCESO AL SISTEMA (Al final)
                Section::make('Seguridad y Cuenta')
                    ->description('Credenciales para el ingreso del estudiante al panel.')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible()
                    ->schema([
                        TextInput::make('user.email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn($record) => $record?->user
                            ),

                        // Lógica de cambio de contraseña para edición
                        Toggle::make('cambiar_password')
                            ->label('¿Desea cambiar la contraseña?')
                            ->columnSpanFull()
                            ->visible(fn($context) => $context === 'edit')
                            ->live()
                            ->dehydrated(false),

                        TextInput::make('user.password')
                            ->label('Contraseña')
                            ->password()
                            ->confirmed()
                            ->required(fn($context) => $context === 'create')
                            ->visible(fn($get, $context) => $context === 'create' || $get('cambiar_password'))
                            ->dehydrated(fn($state) => filled($state)),

                        TextInput::make('user.password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->required(fn($context) => $context === 'create')
                            ->visible(fn($get, $context) => $context === 'create' || $get('cambiar_password'))
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }
}
