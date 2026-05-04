<?php

namespace App\Filament\Resources\AcademicCycles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AcademicCycleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        // PESTAÑA 1: CONFIGURACIÓN PRINCIPAL
                        Tab::make('Configuración del Ciclo')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->label('Nombre del Ciclo')
                                            ->placeholder('Ej. Verano 2026')
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('año')
                                            ->label('Año')
                                            ->numeric()
                                            ->default(now()->year)
                                            ->required()
                                            ->columnSpan(1),

                                        TextInput::make('numero')
                                            ->label('Número de Ciclo')
                                            ->placeholder('Ej. 1')
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('precio')
                                            ->label('Precio')
                                            ->prefix('S/ ')
                                            ->numeric()
                                            ->required(),

                                        DatePicker::make('fecha_inicio')
                                            ->label('Fecha de Inicio')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),

                                        DatePicker::make('fecha_fin')
                                            ->label('Fecha de Fin')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->after('fecha_inicio'),
                                    ]),

                                Toggle::make('estado')
                                    ->label('Ciclo Activo')
                                    ->default(true)
                                    ->helperText('Habilite este ciclo para permitir nuevas matrículas.')
                                    ->inline(false),
                            ]),

                        // PESTAÑA 2: DETALLES / CARACTERÍSTICAS (REPEATER)
                        Tab::make('Detalles y Beneficios')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Repeater::make('details')
                                    ->relationship() // Conecta automáticamente con la relación hasMany('details') en tu modelo
                                    ->label('Lista de características')
                                    ->addActionLabel('Agregar nueva característica')
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->label('Característica')
                                            ->required()
                                            ->placeholder('Ej. Acceso a biblioteca virtual')
                                            ->columnSpan(2),

                                        Select::make('icono')
                                            ->label('Icono Representativo')
                                            ->searchable()
                                            ->allowHtml()
                                            ->options(function () {
                                                // Tu lista curada y específica basada en el diseño
                                                $iconosCurados = [
                                                    'heroicon-o-calendar-days' => 'Calendario (Duración)',
                                                    'heroicon-o-document-text' => 'Documento (Temario)',
                                                    'heroicon-o-users' => 'Usuarios (Asesoramiento)',
                                                    'heroicon-o-globe-alt' => 'Mundo (Sesiones online)',
                                                    'heroicon-o-video-camera' => 'Cámara (Sesiones grabadas)',
                                                    'heroicon-o-book-open' => 'Libro (Biblioteca virtual)',
                                                    'heroicon-o-clipboard-document-check' => 'Checklist (Exámenes)',
                                                    'heroicon-o-chat-bubble-left-ellipsis' => 'Chat (Grupo de WhatsApp)',
                                                    'heroicon-o-calculator' => 'Calculadora (Mate-Básica)',
                                                ];

                                                $opciones = [];

                                                foreach ($iconosCurados as $clase => $nombre) {
                                                    try {
                                                        // Usamos svg() que es rapidísimo y no sobrecarga el servidor
                                                        // Forzamos el ancho y alto estricto con CSS puro (24x24 píxeles)
                                                        $svg = svg($clase, ['style' => 'width: 24px; height: 24px; flex-shrink: 0; color: #6b7280;'])->toHtml();

                                                        // Usamos flexbox clásico en CSS para alinear el icono y el texto
                                                        $opciones[$clase] = "<div style='display: flex; align-items: center; gap: 10px;'>{$svg} <span>{$nombre}</span></div>";
                                                    } catch (\Exception $e) {
                                                        // Si falla un icono por alguna razón, ponemos el nombre en texto plano
                                                        $opciones[$clase] = $nombre;
                                                    }
                                                }

                                                return $opciones;
                                            })
                                            ->columnSpan(1)
                                            ->required(),

                                        Select::make('estado')
                                            ->options([
                                                'Activo' => 'Activo',
                                                'Inactivo' => 'Inactivo',
                                            ])
                                            ->default('Activo')
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4) // Mantiene los campos del repeater en una sola fila
                                    ->defaultItems(0) // Comienza sin elementos vacíos por defecto
                                    ->reorderableWithDragAndDrop(true)
                                    ->orderColumn('orden') // Permite reordenar y guarda el orden en esta columna
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
