{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las
     clases Tailwind del <div class="vd">) para volver al CSS clásico. --}}
{{--
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/video.css') }}">
@endpush
--}}
<x-filament-panels::page>
    <div class="font-handlee pb-[60px] text-[#0c0b1a] dark:text-[#edecf8]">

        {{-- MASTHEAD ── --}}
        <div class="mb-[5px] flex flex-wrap items-center justify-between gap-5 border-b border-[#6366f1]/14 pb-2.5 dark:border-[#818cf8]/12">
            <div class="min-w-0 flex-1">
                <p class="m-0 mb-1.5 text-[0.62rem] font-bold uppercase tracking-[0.15em] text-[#5b5ef4] dark:text-[#818cf8]">Cepre Vallejo · Recursos Académicos</p>
                <h1 class="m-0 text-[clamp(1.8rem,4vw,2.8rem)] font-extrabold leading-[1.1] tracking-[-0.02em] text-[#0c0b1a] dark:text-[#edecf8]">Videoteca <em class="italic text-[#5b5ef4] dark:text-[#818cf8]">Digital</em></h1>
                <p class="mt-2 text-[0.72rem] font-semibold text-[#8b88b0] dark:text-[#636086]">
                    {{ $this->favoritos->count() + $this->videosTotal }} video{{ ($this->favoritos->count() + $this->videosTotal) !== 1 ? 's' : '' }} disponible{{ ($this->favoritos->count() + $this->videosTotal) !== 1 ? 's' : '' }}
                </p>
            </div>
            <div class="flex h-[60px] w-[60px] shrink-0 items-center justify-center rounded-2xl border border-red-600/22 bg-red-600/7 dark:border-red-400/22 dark:bg-red-400/9">
                <svg class="h-7 w-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                        d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </div>
        </div>

        {{-- TOOLBAR ── --}}
        <div class="mb-7 flex flex-wrap items-center gap-3">
            <div class="relative min-w-[180px] max-w-[360px] flex-1">
                <svg class="pointer-events-none absolute left-[13px] top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-[#8b88b0] dark:text-[#636086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                    class="w-full rounded-[14px] border border-[#6366f1]/14 bg-[#f9f8ff] py-[9px] pr-[13px] pl-[38px] text-[0.83rem] text-[#0c0b1a] outline-none transition-colors duration-200 placeholder:text-[#8b88b0] focus:border-[#5b5ef4] focus:bg-white focus:shadow-[0_0_0_3px_rgba(91,94,244,0.07)] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e] dark:text-[#edecf8] dark:placeholder:text-[#636086] dark:focus:border-[#818cf8] dark:focus:bg-[#151424] dark:focus:shadow-[0_0_0_3px_rgba(129,140,248,0.08)]"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por título, área…">
            </div>

            <div class="flex gap-[7px] overflow-x-auto pb-0.5">
                @php
                    $chipBase = 'whitespace-nowrap rounded-full border px-3.5 py-1.5 text-[0.72rem] font-semibold transition-all duration-150';
                    $chipActive = 'border-[#5b5ef4] bg-[#5b5ef4] text-white shadow-[0_3px_10px_rgba(91,94,244,0.18)] dark:border-[#818cf8] dark:bg-[#818cf8] dark:shadow-[0_3px_10px_rgba(129,140,248,0.22)]';
                    $chipInactive = 'border-[#6366f1]/14 bg-[#f9f8ff] text-[#8b88b0] hover:border-[#5b5ef4]/25 hover:bg-[#5b5ef4]/7 hover:text-[#5b5ef4] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e] dark:text-[#636086] dark:hover:border-[#818cf8]/25 dark:hover:bg-[#818cf8]/8 dark:hover:text-[#818cf8]';
                @endphp
                <button class="{{ $chipBase }} {{ is_null($areaId) ? $chipActive : $chipInactive }}"
                    wire:click="$set('areaId', null)">Todos</button>
                @foreach ($this->areas as $area)
                    <button class="{{ $chipBase }} {{ $areaId == $area->id ? $chipActive : $chipInactive }}"
                        wire:click="$set('areaId', {{ $area->id }})">
                        {{ $area->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- FAVORITOS ── --}}
        @if ($this->favoritos->count() > 0 && !$search && !$areaId)
            <div class="mb-[18px] flex items-center gap-3.5">
                <h2 class="m-0 whitespace-nowrap text-[1.2rem] font-bold text-[#0c0b1a] dark:text-[#edecf8]">❤️ Mis favoritos</h2>
                <span class="h-px flex-1 bg-[#6366f1]/14 dark:bg-[#818cf8]/12"></span>
                <span class="whitespace-nowrap text-[0.67rem] font-bold uppercase tracking-[0.1em] text-[#8b88b0] dark:text-[#636086]">
                    {{ $this->favoritos->count() }} video{{ $this->favoritos->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3.5 min-[560px]:grid-cols-3 min-[560px]:gap-[18px] min-[900px]:grid-cols-4 min-[900px]:gap-[22px] min-[1200px]:grid-cols-5">
                @foreach ($this->favoritos as $video)
                    @include('filament.alumno.pages.parts.video-card', [
                        'video'      => $video,
                        'isFavorite' => true,
                    ])
                @endforeach
            </div>

            <hr class="my-11 border-t border-[#6366f1]/14 dark:border-[#818cf8]/12">
        @endif

        {{-- GALERÍA GENERAL ── --}}
        <div class="mb-[18px] flex items-center gap-3.5">
            <h2 class="m-0 whitespace-nowrap text-[1.2rem] font-bold text-[#0c0b1a] dark:text-[#edecf8]">🎬 Explorar Videos</h2>
            <span class="h-px flex-1 bg-[#6366f1]/14 dark:bg-[#818cf8]/12"></span>
            <span class="whitespace-nowrap text-[0.67rem] font-bold uppercase tracking-[0.1em] text-[#8b88b0] dark:text-[#636086]">
                {{ $this->videosTotal }} video{{ $this->videosTotal !== 1 ? 's' : '' }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3.5 min-[560px]:grid-cols-3 min-[560px]:gap-[18px] min-[900px]:grid-cols-4 min-[900px]:gap-[22px] min-[1200px]:grid-cols-5">
            @forelse ($this->videos as $video)
                @include('filament.alumno.pages.parts.video-card', [
                    'video'      => $video,
                    'isFavorite' => $video->isFavoritedBy(auth()->user()),
                ])
            @empty
                <div class="col-span-full rounded-[14px] border-[1.5px] border-dashed border-[#6366f1]/28 bg-[#f9f8ff] px-8 py-[72px] text-center dark:border-[#818cf8]/28 dark:bg-[#1b1a2e]">
                    <svg class="mx-auto mb-3.5 h-11 w-11 text-[#5b5ef4] opacity-[0.22] dark:text-[#818cf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                    <h3 class="m-0 mb-1 text-[0.9rem] font-bold text-[#3b3866] dark:text-[#a9a6cc]">Sin resultados</h3>
                    <p class="m-0 text-[0.75rem] text-[#8b88b0] dark:text-[#636086]">No encontramos videos que coincidan con tu búsqueda.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- MODAL PLAYER ── --}}
    <div class="fixed inset-0 z-[9000] hidden items-center justify-center bg-black/72 p-5 backdrop-blur-[4px] [&.open]:flex" id="vd-modal" onclick="vdCloseModal(event)">
        <div class="animate-modal-in w-full max-w-[820px] overflow-hidden rounded-[18px] bg-white shadow-[0_24px_60px_rgba(0,0,0,0.4)] dark:bg-[#151424]">
            <div class="flex items-start justify-between gap-3 border-b border-[#6366f1]/14 px-5 pt-[18px] pb-3.5 dark:border-[#818cf8]/12">
                <h3 class="m-0 text-[0.95rem] leading-[1.35] font-bold text-[#0c0b1a] dark:text-[#edecf8]" id="vd-modal-title">—</h3>
                <button class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-[#6366f1]/14 bg-[#f9f8ff] transition-colors duration-150 hover:bg-[#eeecfb] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e] dark:hover:bg-[#221f3a]" onclick="vdClose()">
                    <svg class="h-3.5 w-3.5 text-[#8b88b0] dark:text-[#636086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="aspect-video bg-black">
                <iframe id="vd-modal-iframe" src="" allowfullscreen class="block h-full w-full border-none"
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