<style>
    /* ═══════════════════════════════════════
       LIBRO CARD — Estilos Integrados
    ═══════════════════════════════════════ */
    .libro-card {
        background: var(--surface, #ffffff);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border, rgba(99, 102, 241, .14));
        display: flex;
        flex-direction: column;
        position: relative;
        /* Necesario para el enlace expandido */
        height: 100%;
        box-shadow: var(--sh-sm, 0 1px 4px rgba(79, 70, 229, .07));
        transition: transform .35s cubic-bezier(.4, 0, .2, 1),
            box-shadow .35s cubic-bezier(.4, 0, .2, 1),
            border-color .2s;
        cursor: pointer;
    }

    .dark .libro-card {
        background: var(--surface, #151424);
        border-color: var(--border, rgba(129, 140, 248, .12));
    }

    /* Enlace invisible que expande el clic a toda la card */
    .libro-stretched-link {
        position: absolute;
        inset: 0;
        z-index: 1;
        /* Por debajo del botón de favorito */
    }

    @media (hover: hover) {
        .libro-card:hover {
            transform: translateY(-5px) rotate(-.25deg);
            box-shadow: 0 16px 40px rgba(79, 70, 229, .13), 0 4px 12px rgba(79, 70, 229, .07);
            border-color: var(--accent-bd, rgba(91, 94, 244, .25));
        }
    }

    /* ── Portada ── */
    .portada-container {
        aspect-ratio: 3 / 4;
        position: relative;
        overflow: hidden;
        background: var(--surface3, #eeecfb);
    }

    .portada-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .55s cubic-bezier(.4, 0, .2, 1);
    }

    .libro-card:hover .portada-img {
        transform: scale(1.07);
    }

    .no-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: var(--surface3, #eeecfb);
        color: var(--muted, #8b88b0);
        font-family: 'Handlee', sans-serif;
        font-size: 0.68rem;
    }

    .no-image-placeholder svg {
        width: 34px;
        height: 34px;
        color: var(--accent, #5b5ef4);
        opacity: .3;
    }

    /* ── Botón Favorito (Independiente) ── */
    .fav-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, .9);
        border: none;
        border-radius: 9px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(8px);
        transition: transform .2s cubic-bezier(.34, 1.56, .64, 1), background .2s;
        z-index: 10;
        /* Siempre por encima del enlace */
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        padding: 0;
    }

    .dark .fav-btn {
        background: rgba(30, 28, 55, .9);
    }

    .fav-btn:hover {
        transform: scale(1.15);
    }

    .fav-btn svg {
        width: 17px;
        height: 17px;
    }

    /* ── Información ── */
    .libro-info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .libro-area {
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--accent, #5b5ef4);
        background: var(--accent-bg, rgba(91, 94, 244, .07));
        padding: 3px 9px;
        border-radius: 6px;
        width: fit-content;
        border: 1px solid var(--accent-bd, rgba(91, 94, 244, .2));
    }

    .libro-titulo {
        font-family: 'Handlee', serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text1, #0c0b1a);
        margin: 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        letter-spacing: -.01em;
    }

    .libro-autor {
        font-size: 0.75rem;
        color: var(--muted, #8b88b0);
        font-style: italic;
        margin-top: auto;
        padding-top: 8px;
    }
</style>

<div class="libro-card">

    {{-- Enlace que cubre toda la tarjeta --}}
    <a href="{{ $libro->url }}" target="_blank" rel="noopener" class="libro-stretched-link"
        title="Leer {{ $libro->nombre }}">
    </a>

    {{-- Portada --}}
    <div class="portada-container">
        @if ($libro->image_path)
            <img src="{{ asset('storage/' . $libro->image_path) }}" class="portada-img" alt="{{ $libro->nombre }}"
                loading="lazy">
        @else
            <div class="no-image-placeholder">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Sin portada</span>
            </div>
        @endif

        {{-- Botón favorito: Usamos .stop para que el clic no active el enlace del libro --}}
        <button class="fav-btn" wire:click.stop="toggleFavorite({{ $libro->id }})" type="button">
            <svg fill="{{ $isFavorite ? '#dc2626' : 'none' }}" stroke="{{ $isFavorite ? '#dc2626' : '#8b88b0' }}"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>
    </div>

    {{-- Info --}}
    <div class="libro-info">
        <span class="libro-area">{{ $libro->area?->nombre ?? 'General' }}</span>
        <h3 class="libro-titulo">{{ $libro->nombre }}</h3>
        <p class="libro-autor">{{ $libro->autor ?? 'Autor Institucional' }}</p>
    </div>

</div>
