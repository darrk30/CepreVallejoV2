@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reproductor-video.css') }}">
@endpush
<x-filament-panels::page>
    <div class="vp-root">
        <div class="vp-layout">

            {{-- ══ REPRODUCTOR + INFO ══ --}}
            <div>
                <div class="vp-player-wrap">
                    <iframe src="{{ $this->getEmbedUrl() }}" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                        style="width: 100%; aspect-ratio: 16/9; border-radius: 12px; border: none;">
                    </iframe>
                </div>

                <div class="vp-info">
                    <div class="vp-area-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:10px;height:10px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                        </svg>
                        {{ $video->area?->nombre ?? 'General' }}
                    </div>

                    <h1 class="vp-title">{{ $video->titulo }}</h1>

                    <div class="vp-meta">
                        @if ($video->autor)
                            <span class="vp-meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $video->autor }}
                            </span>
                        @endif
                        <span class="vp-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 013.741-1.234"/></svg>
                            Institución Cepre Vallejo
                        </span>
                    </div>

                    @if ($video->descripcion)
                        <div class="vp-description">{{ $video->descripcion }}</div>
                    @endif
                </div>
            </div>

            {{-- ══ SIDEBAR RECOMENDADOS ══ --}}
            <div class="vp-sidebar">
                <h2 class="vp-sidebar-title">A continuación</h2>

                @foreach($this->recommended_videos as $i => $rec)
                    @php
                        $recThumb = $rec->image_path ? asset('storage/' . $rec->image_path) : null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $rec->url, $match)) {
                            $recThumb = "https://img.youtube.com/vi/{$match[1]}/mqdefault.jpg";
                        }
                    @endphp

                    @if ($i > 0)
                        <div class="rec-divider"></div>
                    @endif

                    <a href="{{ \App\Filament\Alumno\Pages\ReproductorVideo::getUrl(['videoSlug' => $rec->slug]) }}"
                       class="rec-card">
                        <div class="rec-thumb-wrap">
                            @if ($recThumb)
                                <img src="{{ $recThumb }}" class="rec-thumb-img" alt="{{ $rec->titulo }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--surface3);">
                                    <svg style="width:20px;height:20px;color:var(--muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            @endif
                            <div class="rec-play">
                                <div class="rec-play-circle">
                                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 2.691l11 7.309-11 7.309V2.691z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="rec-info">
                            <span class="rec-area">{{ $rec->area?->nombre ?? 'General' }}</span>
                            <h4 class="rec-name">{{ $rec->titulo }}</h4>
                            @if ($rec->descripcion)
                                <p class="rec-desc">{{ $rec->descripcion }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-filament-panels::page>