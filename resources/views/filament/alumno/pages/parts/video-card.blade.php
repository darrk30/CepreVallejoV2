@php
    $youtubeId    = null;
    $thumbnailUrl = $video->image_path ? asset('storage/' . $video->image_path) : null;

    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video->url, $match)) {
        $youtubeId    = $match[1];
        $thumbnailUrl = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
    }
@endphp

<style>
    /* ═══════════════════════════════════════
       VIDEO CARD — Estilos Integrados
    ═══════════════════════════════════════ */
    .video-card {
        background: var(--surface, #fff);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border, rgba(99,102,241,.14));
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative; /* Crítico para el stretched link */
        box-shadow: var(--sh-sm, 0 1px 4px rgba(79,70,229,.07));
        transition: transform .32s cubic-bezier(.4,0,.2,1),
                    box-shadow .32s cubic-bezier(.4,0,.2,1),
                    border-color .2s;
        cursor: pointer;
    }

    .dark .video-card {
        background: var(--surface, #151424);
        border-color: var(--border, rgba(129,140,248,.12));
    }

    /* Enlace que expande el clic a toda la card */
    .video-stretched-link {
        position: absolute;
        inset: 0;
        z-index: 1; /* Por debajo del botón de favorito */
    }

    @media (hover: hover) {
        .video-card:hover {
            transform: translateY(-5px) rotate(-.2deg);
            box-shadow: 0 16px 40px rgba(79,70,229,.13), 0 4px 12px rgba(79,70,229,.07);
            border-color: var(--accent-bd, rgba(91,94,244,.25));
        }
    }

    /* ── Thumbnail ── */
    .video-thumb-container {
        aspect-ratio: 16 / 9;
        position: relative;
        overflow: hidden;
        background: var(--surface3, #eeecfb);
    }

    .video-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .5s cubic-bezier(.4,0,.2,1);
    }

    .video-card:hover .video-thumb-img { 
        transform: scale(1.07); 
    }

    /* Overlay play */
    .play-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(11,10,26,.3);
        opacity: 0;
        transition: opacity .25s;
    }

    .video-card:hover .play-overlay { 
        opacity: 1; 
    }

    .play-circle {
        width: 48px;
        height: 48px;
        background: var(--accent, #5b5ef4);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px var(--accent-gl, rgba(91,94,244,.35));
        transition: transform .2s;
    }

    .play-circle svg { 
        width: 20px; 
        height: 20px; 
        color: #fff; 
        margin-left: 3px; 
    }

    /* ── Botón Favorito (Independiente) ── */
    .video-fav-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10; /* Superior al enlace mask */
        background: rgba(255,255,255,.9);
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(8px);
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
        transition: transform .2s cubic-bezier(.34,1.56,.64,1);
        padding: 0;
    }

    .dark .video-fav-btn { 
        background: rgba(30,28,55,.9); 
    }

    .video-fav-btn:hover { 
        transform: scale(1.15); 
    }

    .video-fav-btn svg { 
        width: 17px; 
        height: 17px; 
    }

    /* ── Cuerpo ── */
    .video-info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .video-area-badge {
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--accent, #5b5ef4);
        background: var(--accent-bg, rgba(91,94,244,.07));
        border: 1px solid var(--accent-bd, rgba(91,94,244,.2));
        padding: 2px 8px;
        border-radius: 5px;
        width: fit-content;
        margin-bottom: 4px;
    }

    .video-card-title {
        font-family: 'Handlee', serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text1, #0c0b1a);
        line-height: 1.35;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        letter-spacing: -.01em;
    }

    .video-card-description {
        font-size: 0.73rem;
        color: var(--muted, #8b88b0);
        line-height: 1.5;
        margin-top: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-style: italic;
    }
</style>

<div class="video-card">

    {{-- Enlace que cubre toda la tarjeta --}}
    <a href="{{ \App\Filament\Alumno\Pages\ReproductorVideo::getUrl(['videoSlug' => $video->slug]) }}"
       class="video-stretched-link"
       title="Ver {{ $video->titulo }}">
    </a>

    {{-- Thumbnail --}}
    <div class="video-thumb-container">
        @if($thumbnailUrl)
            <img src="{{ $thumbnailUrl }}"
                 class="video-thumb-img"
                 alt="{{ $video->titulo }}"
                 loading="lazy">
        @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                <svg style="width:36px;height:36px;color:var(--accent);opacity:.25;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        @endif

        {{-- Icono Play al Hover --}}
        <div class="play-overlay">
            <div class="play-circle">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4.5 2.691l11 7.309-11 7.309V2.691z"/>
                </svg>
            </div>
        </div>

        {{-- Botón Favorito (Independiente) --}}
        <button class="video-fav-btn"
            wire:click.stop="toggleFavorite({{ $video->id }})"
            type="button"
            aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
            <svg fill="{{ $isFavorite ? '#dc2626' : 'none' }}"
                 stroke="{{ $isFavorite ? '#dc2626' : '#8b88b0' }}"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    {{-- Información --}}
    <div class="video-info">
        <span class="video-area-badge">{{ $video->area?->nombre ?? 'General' }}</span>

        <h3 class="video-card-title">{{ $video->titulo }}</h3>

        @if ($video->descripcion)
            <p class="video-card-description">{{ $video->descripcion }}</p>
        @endif
    </div>

</div>