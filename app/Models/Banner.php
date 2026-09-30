<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

            return $response->successful() ? $response->json('thumbnail_url') : null;
        } catch (\Throwable $e) {
            report($e);

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