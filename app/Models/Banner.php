<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'imagen_desktop_path',
        'imagen_mobile_path',
        'enlace',
        'tipo',
        'orden',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($banner) {
            if (Auth::check()) {
                $banner->user_create_id = Auth::id();
            }

            // El banner nuevo siempre va primero: corremos un puesto hacia
            // atrás a todos los existentes y el nuevo toma el puesto 1.
            // (Antes se agregaba al final con max()+1).
            if (is_null($banner->orden)) {
                static::query()->increment('orden');
                $banner->orden = 1;
            }
        });

        // Se dispara al Crear y al Editar (justo antes del INSERT/UPDATE),
        // no mientras se llena el formulario — así "Crear"/"Guardar"
        // responde al toque, sin esperar a que TikTok conteste.
        static::saving(function (Banner $banner) {
            if (! self::tiktokVideoId($banner->enlace)) {
                return;
            }

            $linkCambio = $banner->isDirty('enlace');
            $noTieneImagen = ! $banner->imagen_desktop_path;

            // No hay nada que hacer si el link sigue igual Y ya tiene una
            // imagen puesta — así no se llama a TikTok de nuevo sin
            // necesidad en cada guardado.
            if (! $linkCambio && ! $noTieneImagen) {
                return;
            }

            // Si en este mismo guardado también subieron una imagen a mano
            // (junto con el link nuevo, o para rellenar la que faltaba), se
            // respeta esa elección.
            if ($banner->imagen_desktop_path && $banner->isDirty('imagen_desktop_path')) {
                return;
            }

            // Cambiaste el link a otro video, lo pusiste por primera vez, o
            // es un banner viejo al que nunca se le pudo bajar la miniatura
            // (de antes de tener esta función): se descarga la miniatura
            // del video ACTUAL, reemplazando la que hubiera antes.
            $path = self::downloadTiktokThumbnail($banner->enlace);

            if ($path) {
                $banner->imagen_desktop_path = $path;

                return;
            }

            // No se pudo — el motivo exacto queda en storage/logs/laravel.log.
            // Igual dejamos guardar el banner (sin imagen), para no bloquear
            // al admin; puede subirla a mano o reintentar guardando de nuevo.
            if (class_exists(\Filament\Notifications\Notification::class) && Auth::check()) {
                \Filament\Notifications\Notification::make()
                    ->title('No se pudo descargar la miniatura de TikTok')
                    ->body('Revisa que el link sea correcto y vuelve a guardar para reintentar. Si sigue fallando, puedes subir la imagen a mano.')
                    ->warning()
                    ->send();
            }
        });

        static::saved(fn () => Cache::forget('home.page.data'));
        static::deleted(fn () => Cache::forget('home.page.data'));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Si $enlace es un video de TikTok (ej. tiktok.com/@usuario/video/123...),
     * devuelve su ID; si no, null. Fuente única usada tanto por el
     * formulario del admin (para no exigir imagen) como por la home
     * pública (para saber si debe pedir la miniatura a oEmbed).
     */
    public static function tiktokVideoId(?string $enlace): ?string
    {
        if (! $enlace) {
            return null;
        }

        if (preg_match('#tiktok\.com/@[\w.\-]+/video/(\d+)#i', $enlace, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Pide la miniatura del video a la API pública oEmbed de TikTok
     * (https://www.tiktok.com/oembed?url=...). No requiere API key. Se
     * llama al vuelo, sin descargar ni guardar nada en el servidor —
     * quien la use debe cachear el resultado del lado que corresponda.
     */
    public static function fetchTiktokThumbnail(string $enlace): ?string
    {
        try {
            $response = Http::timeout(5)->get('https://www.tiktok.com/oembed', ['url' => $enlace]);

            if (! $response->successful()) {
                Log::warning('TikTok oEmbed falló', [
                    'enlace' => $enlace,
                    'status' => $response->status(),
                    'body' => str($response->body())->limit(300)->toString(),
                ]);

                return null;
            }

            return $response->json('thumbnail_url');
        } catch (\Throwable $e) {
            Log::error('TikTok oEmbed: excepción al llamar', [
                'enlace' => $enlace,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Descarga la miniatura del video (vía oEmbed) y la guarda en
     * storage/banners/desktop, igual que una imagen subida a mano. Devuelve
     * la ruta relativa (para meterla directo en imagen_desktop_path) o null
     * si algo falla (link inválido, oEmbed no respondió, etc.).
     *
     * Se usa una sola vez, al pegar el link en el formulario del admin —
     * después de eso el banner ya tiene su propio archivo local y no
     * depende de una nueva llamada a TikTok para mostrarse.
     */
    public static function downloadTiktokThumbnail(string $enlace): ?string
    {
        $videoId = self::tiktokVideoId($enlace);

        if (! $videoId) {
            return null;
        }

        try {
            $thumbnailUrl = self::fetchTiktokThumbnail($enlace);

            if (! $thumbnailUrl) {
                // Ya se registró el motivo dentro de fetchTiktokThumbnail().
                return null;
            }

            $response = Http::timeout(10)->get($thumbnailUrl);

            if (! $response->successful()) {
                Log::warning('TikTok: no se pudo descargar la imagen de la miniatura', [
                    'video_id' => $videoId,
                    'thumbnail_url' => $thumbnailUrl,
                    'status' => $response->status(),
                ]);

                return null;
            }

            // TikTok no siempre manda jpg (a veces es png/webp); nos fijamos
            // en el Content-Type real de la respuesta en vez de asumir.
            $extension = match (true) {
                str_contains($response->header('Content-Type') ?? '', 'png') => 'png',
                str_contains($response->header('Content-Type') ?? '', 'webp') => 'webp',
                default => 'jpg',
            };

            $path = "banners/desktop/tiktok-{$videoId}.{$extension}";
            $guardado = Storage::disk('public')->put($path, $response->body());

            if (! $guardado) {
                Log::error('TikTok: la imagen se descargó pero no se pudo guardar en storage', [
                    'video_id' => $videoId,
                    'path' => $path,
                    'disk_path' => Storage::disk('public')->path($path),
                ]);

                return null;
            }

            return $path;
        } catch (\Throwable $e) {
            Log::error('TikTok: excepción al descargar/guardar la miniatura', [
                'enlace' => $enlace,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Proporción ancho/alto real del archivo ya subido, o null si no se
     * puede leer. Es un archivo local (rápido), no una petición externa.
     */
    protected static function imageAspectRatio(?string $path): ?float
    {
        if (! $path) {
            return null;
        }

        try {
            $fullPath = Storage::disk('public')->path($path);

            if (! is_file($fullPath)) {
                return null;
            }

            $size = @getimagesize($fullPath);

            if (! $size || ! $size[1]) {
                return null;
            }

            [$width, $height] = $size;

            return $width / $height;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * ¿La imagen es claramente panorámica (mucho más ancha que alta)? Se usa
     * para que, en la galería de la home, ocupe 2 columnas del grid en vez
     * de 1.
     */
    public static function isWideImage(?string $path): bool
    {
        return (self::imageAspectRatio($path) ?? 1) >= 1.35;
    }

    /**
     * ¿La imagen es claramente alta/vertical (mucho más alta que ancha)? Se
     * usa para que, en la galería de la home, ocupe 2 filas del grid (más
     * alto) en vez de 1 — así no se recorta de más al forzarla a una celda
     * casi cuadrada.
     */
    public static function isTallImage(?string $path): bool
    {
        return (self::imageAspectRatio($path) ?? 1) <= 0.75;
    }
}