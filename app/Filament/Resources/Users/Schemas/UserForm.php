<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // SECCIÓN 1: DATOS PERSONALES
                        Section::make('Información del Usuario')
                            ->description('Datos básicos y estado de la cuenta.')
                            ->columnSpan(2)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-user'),

                                TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-envelope'),
                                
                                Select::make('roles')
                                    ->label('Asignar Roles')
                                    ->relationship('roles', 'name') // Relación con Spatie
                                    ->multiple() // Quitar si solo quieres permitir un rol por usuario
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->prefixIcon('heroicon-m-shield-check'),

                                Select::make('estado')
                                    ->label('Estado de la cuenta')
                                    ->options([
                                        'activo' => 'Activo',
                                        'inactivo' => 'Inactivo',
                                    ])
                                    ->default('activo')
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-bolt'),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Verificado el')
                                    ->placeholder('Sin verificar'),
                            ]),

                        // SECCIÓN 2: SEGURIDAD (PASSWORD)
                        Section::make('Seguridad')
                            ->description('Gestión de credenciales.')
                            ->columnSpan(1)
                            ->schema([
                                // El "Check" para activar el cambio de contraseña (solo se ve al editar)
                                Toggle::make('change_password')
                                    ->label('¿Cambiar contraseña?')
                                    ->live()
                                    ->hiddenOn('create')
                                    ->dehydrated(false) // No se guarda en la base de datos
                                    ->columnSpanFull(),

                                TextInput::make('password')
                                    ->label('Nueva Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255)
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->visible(fn(Get $get, string $context): bool => $context === 'create' || $get('change_password'))
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
