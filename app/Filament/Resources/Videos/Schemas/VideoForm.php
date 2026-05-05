<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Models\Video;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('url')
                    ->label('Enlace de YouTube')
                    ->url()
                    ->required()
                    ->live(onBlur: true) // Se dispara al perder el foco del campo
                    ->prefixIcon('heroicon-m-play')
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if (empty($state)) return;

                        // Verificamos que sea un link de YouTube
                        if (Str::contains($state, ['youtube.com', 'youtu.be'])) {
                            $response = Http::get("https://www.youtube.com/oembed?url={$state}&format=json");

                            if ($response->successful()) {
                                $data = $response->json();

                                // Solo llena el título si está vacío para no borrar cambios manuales
                                if (blank($get('titulo'))) {
                                    $set('titulo', $data['title'] ?? '');
                                }

                                // Para la miniatura, guardamos la URL externa directamente
                                // Si prefieres descargarla al servidor, se requiere lógica adicional de Storage
                                if (blank($get('image_path'))) {
                                    $set('image_path', $data['thumbnail_url'] ?? '');
                                }
                            }
                        }
                    }),

                TextInput::make('titulo')
                    ->label('Título del Video')
                    ->required()
                    ->maxLength(255),

                TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->default(function () {
                        $nextId = (Video::max('id') ?? 0) + 1;
                        return "VID-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                    })
                    ->unique('videos', 'codigo', ignoreRecord: true),

                Select::make('area_id')
                    ->label('Área Académica')
                    ->relationship('area', 'nombre')
                    ->required()
                    ->searchable(),

                Textarea::make('descripcion')
                    ->columnSpanFull(),

                // IMPORTANTE: Si vas a guardar la URL de la miniatura de YouTube, 
                // usa TextInput en lugar de FileUpload, o mantén FileUpload 
                // pero ten en cuenta que espera un archivo local.
                TextInput::make('image_path')
                    ->label('URL de la Miniatura')
                    ->helperText('Se obtiene automáticamente de YouTube'),

                Select::make('estado')
                    ->options(['activo' => 'Activo', 'inactivo' => 'Inactivo'])
                    ->default('activo')
                    ->required(),

                Placeholder::make('video_preview')
                    ->label('Vista Previa del Video')
                    ->content(function (Get $get) {
                        $url = $get('url');
                        if (!$url) return 'Ingresa un link de YouTube para ver la previa.';

                        // Convertimos el link normal a link de embed
                        $videoId = null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
                            $videoId = $match[1];
                        }

                        if (!$videoId) return 'Link no válido.';

                        return new \Illuminate\Support\HtmlString("
                            <div style='width: 100%; max-width: 500px; margin: 0 auto;'>
                                <div style='
                                    position: relative; 
                                    width: 100%; 
                                    aspect-ratio: 16 / 9; 
                                    border-radius: 12px; 
                                    overflow: hidden; 
                                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                                    background: #000;
                                '>
                                    <iframe 
                                        src='https://www.youtube.com/embed/{$videoId}' 
                                        style='
                                            position: absolute; 
                                            top: 0; 
                                            left: 0; 
                                            width: 100%; 
                                            height: 100%; 
                                            border: none;
                                        '
                                        allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' 
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        ");
                    })->columnSpanFull(),


            ]);
    }
}
