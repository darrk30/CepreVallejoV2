@push('styles')
    <link rel="stylesheet" href="{{ asset('css/video.css') }}">
@endpush
<x-filament-panels::page>
    <div class="vd">

        {{-- MASTHEAD ── --}}
        <div class="vd-masthead">
            <div class="vd-masthead-left">
                <p class="vd-masthead-eyebrow">Cepre Vallejo · Recursos Académicos</p>
                <h1 class="vd-masthead-title">Videoteca <em>Digital</em></h1>
                <p class="vd-masthead-count" id="vd-count">&nbsp;</p>
            </div>
            <div class="vd-masthead-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                        d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
        </div>

        {{-- TOOLBAR ── --}}
        <div class="vd-toolbar">
            <div class="vd-search-wrap">
                <svg class="vd-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" class="vd-search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por título, área…">
            </div>

            <div class="vd-chips">
                <button class="vd-chip {{ is_null($areaId) ? 'active' : '' }}"
                    wire:click="$set('areaId', null)">Todos</button>
                @foreach ($this->areas as $area)
                    <button class="vd-chip {{ $areaId == $area->id ? 'active' : '' }}"
                        wire:click="$set('areaId', {{ $area->id }})">
                        {{ $area->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- FAVORITOS ── --}}
        @if ($this->favoritos->count() > 0 && !$search && !$areaId)
            <div class="vd-section-head">
                <h2 class="vd-section-title">❤️ Mis favoritos</h2>
                <span class="vd-section-line"></span>
                <span class="vd-section-count">
                    {{ $this->favoritos->count() }} video{{ $this->favoritos->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            <div class="vd-grid">
                @foreach ($this->favoritos as $video)
                    @include('filament.alumno.pages.parts.video-card', [
                        'video'      => $video,
                        'isFavorite' => true,
                    ])
                @endforeach
            </div>

            <hr class="vd-separator">
        @endif

        {{-- GALERÍA GENERAL ── --}}
        <div class="vd-section-head">
            <h2 class="vd-section-title">🎬 Explorar Videos</h2>
            <span class="vd-section-line"></span>
            <span class="vd-section-count" id="vd-general-count">
                {{ $this->videos->count() }} video{{ $this->videos->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="vd-grid">
            @forelse ($this->videos as $video)
                @include('filament.alumno.pages.parts.video-card', [
                    'video'      => $video,
                    'isFavorite' => $video->isFavoritedBy(auth()->user()),
                ])
            @empty
                <div class="vd-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                    <h3>Sin resultados</h3>
                    <p>No encontramos videos que coincidan con tu búsqueda.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- MODAL PLAYER ── --}}
    <div class="vd-modal-backdrop" id="vd-modal" onclick="vdCloseModal(event)">
        <div class="vd-modal">
            <div class="vd-modal-head">
                <h3 id="vd-modal-title">—</h3>
                <button class="vd-modal-close" onclick="vdClose()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="vd-modal-player">
                <iframe id="vd-modal-iframe" src="" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin">
                </iframe>
            </div>
        </div>
    </div>

    <script>
        /* ── Embed URL ── */
        function vdEmbed(url) {
            if (!url) return '';
            const yt = url.match(/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|shorts\/|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
            if (yt) return `https://www.youtube-nocookie.com/embed/${yt[1]}?autoplay=1&rel=0&modestbranding=1`;
            const vimeo = url.match(/vimeo\.com\/(\d+)/);
            if (vimeo) return `https://player.vimeo.com/video/${vimeo[1]}?autoplay=1&dnt=1`;
            return url;
        }

        /* ── Abrir modal ── */
        function vdOpen(url, title) {
            document.getElementById('vd-modal-title').textContent  = title || '';
            document.getElementById('vd-modal-iframe').src         = vdEmbed(url);
            document.getElementById('vd-modal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        /* ── Cerrar modal ── */
        function vdClose() {
            document.getElementById('vd-modal').classList.remove('open');
            document.getElementById('vd-modal-iframe').src = '';
            document.body.style.overflow = '';
        }

        function vdCloseModal(e) {
            if (e.target === document.getElementById('vd-modal')) vdClose();
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') vdClose(); });

        /* ── Contador ── */
        function vdUpdateCount() {
            const count = document.querySelectorAll('.vd-card').length;
            const el    = document.getElementById('vd-count');
            if (el) el.textContent = `${count} video${count !== 1 ? 's' : ''} disponible${count !== 1 ? 's' : ''}`;
        }

        document.addEventListener('DOMContentLoaded', vdUpdateCount);
        document.addEventListener('livewire:updated',  vdUpdateCount);
    </script>

</x-filament-panels::page>