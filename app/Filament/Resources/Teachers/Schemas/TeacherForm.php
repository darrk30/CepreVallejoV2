<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECCIÓN 1: IDENTIDAD Y PERFIL (Lo primero que ves)
                Section::make('Identidad del Docente')
                    ->description('Información básica y fotografía del perfil académico.')
                    ->icon('heroicon-o-user-circle') // Icono visual para mejor UX
                    ->schema([
                        Grid::make(3) // Usamos 3 columnas para un diseño más balanceado
                            ->schema([
                                FileUpload::make('imagen_path')
                                    ->label('Foto de Perfil')
                                    ->image()
                                    ->imageEditor()
                                    ->optimize('webp', 80)
                                    ->maxImageWidth(1200)
                                    ->directory('docentes/fotos')
                                    ->avatar()
                                    ->columnSpan(1), // Ocupa 1/3 del espacio

                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('user.name')
                                            ->label('Nombre Completo')
                                            ->placeholder('Ej. Juan Pérez')
                                            ->required(),
                                        TextInput::make('telefono')
                                            ->label('Nro. Telefono')
                                            ->required()
                                            ->maxLength(9)
                                            ->numeric(),
                                        TextInput::make('dni')
                                            ->label('Documento de Identidad (DNI)')
                                            ->required()
                                            ->maxLength(8)
                                            ->numeric(),
                                    ])
                                    ->columnSpan(2), // Ocupa los 2/3 restantes
                            ]),
                    ]),

                // SECCIÓN 2: FORMACIÓN Y ESTADO
                Section::make('Información Académica')
                    ->description('Detalles sobre la especialidad y trayectoria profesional.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Select::make('specialties')
                            ->label('Especialidades asignadas')
                            ->multiple()
                            ->relationship('specialties', 'nombre')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('nombre')->required(),
                            ])
                            ->columnSpan(1),

                        Toggle::make('estado')
                            ->label('Estado del Docente')
                            ->helperText('Define si el docente tiene acceso activo al aula virtual.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1),

                        Textarea::make('biografia')
                            ->label('Resumen Profesional / Biografía')
                            ->rows(4)
                            ->placeholder('Describe brevemente la trayectoria del docente...')
                            ->columnSpanFull(),
                    ])->columns(2),

                // SECCIÓN 3: CREDENCIALES DE ACCESO (Al final, como configuración)
                Section::make('Seguridad y Acceso')
                    ->description('Configuración del correo electrónico y claves de inicio de sesión.')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible() // Esta sección se puede contraer para que el form se vea más corto
                    ->schema([
                        TextInput::make('user.email')
                            ->label('Correo Electrónico Institucional')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn($record) => $record?->user // Ignora al usuario relacionado, no al docente
                            ),

                        Toggle::make('cambiar_password')
                            ->label('Actualizar contraseña')
                            ->visible(fn($context) => $context === 'edit')
                            ->live()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('user.password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->confirmed()
                            ->required(fn($context) => $context === 'create')
                            ->visible(fn($get, $context) => $context === 'create' || $get('cambiar_password'))
                            ->dehydrated(fn($state) => filled($state)),

                        TextInput::make('user.password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->required(fn($context) => $context === 'create')
                            ->visible(fn($get, $context) => $context === 'create' || $get('cambiar_password'))
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }
}
