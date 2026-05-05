<?php

namespace App\Filament\Resources\Libros\Schemas;

use App\Models\Libro;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LibroForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Principal')
                    ->description('Datos básicos y de identificación del libro.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nombre')
                                ->label('Nombre del Libro')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                            TextInput::make('codigo')
                                ->label('Código de Libro')
                                ->required()
                                ->placeholder('Ej: LIB-001')
                                ->default(function () {
                                    $nextId = (Libro::max('id') ?? 0) + 1;
                                    return "LIB-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                                })
                                ->unique('libros', 'codigo', ignoreRecord: true)
                                ->prefixIcon('heroicon-m-hashtag')
                                ->extraInputAttributes(['required' => false]),

                            TextInput::make('autor')
                                ->placeholder('Nombre del autor'),

                            Select::make('area_id')
                                ->label('Área Académica')
                                ->relationship('area', 'nombre')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Archivos y Estado')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('image_path')
                                ->label('Portada (Imagen)')
                                ->image()
                                ->directory('libros/portadas')
                                ->imageEditor()
                                ->columnSpanFull(),

                            TextInput::make('url')
                                ->label('URL del Recurso (PDF/Web)')
                                ->url()
                                ->placeholder('https://...')
                                ->columnSpanFull(),

                            Select::make('estado')
                                ->options([
                                    'activo' => 'Activo',
                                    'inactivo' => 'Inactivo',
                                ])
                                ->default('activo')
                                ->required()
                                ->selectablePlaceholder(false),
                        ]),
                    ]),

                Placeholder::make('libro_preview')
                    ->label('Vista Previa del Libro')
                    ->content(function (Get $get) {
                        $url = $get('url');
                        if (!$url) return 'Ingresa un link de Google Drive.';

                        // Limpieza y preparación del link
                        $embedUrl = str_replace('/view', '/preview', $url);
                        if (str_contains($embedUrl, '?')) {
                            $embedUrl = explode('?', $embedUrl)[0] . '/preview';
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div style='width: 100%; max-width: 500px; margin: 0 auto;'>
                                <div style='position: relative; width: 100%; height: 600px; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; background: #f9fafb;'>
                                    <iframe 
                                        src='{$embedUrl}' 
                                        style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;'>
                                    </iframe>
                                </div>
                            </div>
                        ");
                    })
            ]);
    }
}
