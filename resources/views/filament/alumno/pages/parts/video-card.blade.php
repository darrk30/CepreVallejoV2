@php
    $youtubeId    = null;
    $thumbnailUrl = $video->image_path ? asset('storage/' . $video->image_path) : null;

    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video->url, $match)) {
        $youtubeId    = $match[1];
        $thumbnailUrl = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
    }
@endphp

{{-- Migrado a Tailwind. Si algo falla, descomenta el <style> de abajo (git
     historial) y quita las clases Tailwind del <div class="video-card">. --}}
<div class="group video-card relative flex h-full cursor-pointer flex-col overflow-hidden rounded-xl border border-[#6366f1]/14 bg-white shadow-[0_1px_4px_rgba(79,70,229,0.07),0_1px_2px_rgba(0,0,0,0.04)] transition-[transform,box-shadow,border-color] duration-[320ms] ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-[5px] hover:-rotate-[0.2deg] hover:border-[#5b5ef4]/25 hover:shadow-[0_16px_40px_rgba(79,70,229,0.13),0_4px_12px_rgba(79,70,229,0.07)] dark:border-[#818cf8]/12 dark:bg-[#151424]">

    {{-- Enlace que cubre toda la tarjeta --}}
    <a href="{{ \App\Filament\Alumno\Pages\ReproductorVideo::getUrl(['videoSlug' => $video->slug]) }}"
       class="absolute inset-0 z-[1]"
       title="Ver {{ $video->titulo }}">
    </a>

    {{-- Thumbnail --}}
    <div class="relative aspect-video overflow-hidden bg-[#eeecfb] dark:bg-[#221f3a]">
        @if($thumbnailUrl)
            <img src="{{ $thumbnailUrl }}"
                 class="block h-full w-full object-cover transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] group-hover:scale-[1.07]"
                 alt="{{ $video->titulo }}"
                 loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center">
                <svg class="h-9 w-9 text-[#5b5ef4] opacity-25 dark:text-[#818cf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        @endif

        {{-- Icono Play al Hover --}}
        <div class="absolute inset-0 flex items-center justify-center bg-[#0b0a1a]/30 opacity-0 transition-opacity duration-[250ms] group-hover:opacity-100">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#5b5ef4] shadow-[0_6px_20px_rgba(91,94,244,0.18)] transition-transform duration-200 group-hover:scale-110 dark:bg-[#818cf8] dark:shadow-[0_6px_20px_rgba(129,140,248,0.22)]">
                <svg class="ml-0.5 h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4.5 2.691l11 7.309-11 7.309V2.691z"/>
                </svg>
            </div>
        </div>

        {{-- Botón Favorito (Independiente) --}}
        <button class="absolute top-[10px] right-[10px] z-10 flex h-[34px] w-[34px] items-center justify-center rounded-[9px] border-none bg-white/90 p-0 shadow-[0_2px_8px_rgba(0,0,0,0.12)] backdrop-blur-[8px] transition-transform duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-[1.15] dark:bg-[#1e1c37]/90"
            wire:click.stop="toggleFavorite({{ $video->id }})"
            type="button"
            aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
            <svg class="h-[17px] w-[17px]" fill="{{ $isFavorite ? '#dc2626' : 'none' }}"
                 stroke="{{ $isFavorite ? '#dc2626' : '#8b88b0' }}"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    {{-- Información --}}
    <div class="flex flex-1 flex-col gap-1 p-[15px]">
        <span class="mb-1 w-fit rounded-[5px] border border-[#5b5ef4]/25 bg-[#5b5ef4]/7 px-2 py-0.5 text-[0.58rem] font-bold tracking-[0.1em] text-[#5b5ef4] uppercase dark:border-[#818cf8]/25 dark:bg-[#818cf8]/8 dark:text-[#818cf8]">{{ $video->area?->nombre ?? 'General' }}</span>

        <h3 class="m-0 line-clamp-2 font-handlee text-[0.95rem] leading-[1.35] font-bold tracking-[-0.01em] text-[#0c0b1a] dark:text-[#edecf8]">{{ $video->titulo }}</h3>

        @if ($video->descripcion)
            <p class="mt-1 line-clamp-2 text-[0.73rem] leading-[1.5] text-[#8b88b0] italic dark:text-[#636086]">{{ $video->descripcion }}</p>
        @endif
    </div>

</div>
