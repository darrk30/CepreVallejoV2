<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('js/podcast-swiper.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/mis-podcasts.css') }}">

<x-filament-panels::page>
    @php
        $playlistData = $this->podcasts->map(function($p) {
            return [
                'id' => $p->id,
                'title' => $p->titulo,
                'cover' => $p->imagen_portada ? asset('storage/' . $p->imagen_portada) : null,
                'src' => $p->stream_url,
            ];
        })->values();
    @endphp

    <div class="podcasts-wrapper" x-data x-init=" $store.podcastPlayer.setPlaylist({{ json_encode($playlistData) }}) ">

        <div class="pd-section-header">
            <h1 class="pd-main-title">Nuestros Podcasts</h1>
            <p class="pd-main-subtitle">Escucha el contenido preparado para ti</p>
        </div>

        <div class="pd-divider"></div>

        <!-- Sección de Autores -->
        @if($this->autores->isNotEmpty())
            <h2 class="pd-section-subtitle">Autores</h2>
            <div class="swiper-container authorsSwiper mb-12">
                <div class="swiper-wrapper">
                    @foreach($this->autores as $autor)
                        <div class="swiper-slide autor-slide">
                            <a href="{{ \App\Filament\Alumno\Pages\PodcastsAutor::getUrl(['autor_id' => $autor->id]) }}" class="autor-card">
                                <div class="autor-img">
                                    <img src="{{ $autor->imagen ? asset('storage/'.$autor->imagen) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" alt="{{ $autor->nombre }}">
                                </div>
                                <span class="autor-name">{{ $autor->nombre }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #9ca3af; border: 2px dashed #e5e7eb; border-radius: 16px; margin-bottom: 32px;">
                <p>Aún no hay autores registrados.</p>
            </div>
        @endif

        <div class="pd-divider"></div>

        <!-- Sección de Audios -->
        <h2 class="pd-section-subtitle">Audios</h2>
        
        @if($this->podcasts->isNotEmpty())
            <div class="pd-table-container">
                <table class="pd-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Título</th>
                            <th>Álbum</th>
                            <th>Fecha</th>
                            <th class="text-right">Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->podcasts as $index => $podcast)
                            <!-- ... (Tu código de fila del podcast se mantiene igual) ... -->
                            <tr class="pd-row" x-data="{ podcastData: {{ json_encode(['id' => $podcast->id, 'title' => $podcast->titulo, 'cover' => $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : null, 'src' => $podcast->stream_url]) }}, get isThisPlaying() { return $store.podcastPlayer.current?.id === this.podcastData.id && $store.podcastPlayer.isPlaying; }, get isThisActive() { return $store.podcastPlayer.current?.id === this.podcastData.id; }, handlePlay() { if (this.isThisActive) { $store.podcastPlayer.toggle(); } else { $store.podcastPlayer.play(this.podcastData); } } }" :class="{ 'pd-row--active': isThisActive }" @click="handlePlay()">
                                <td class="pd-index-col">
                                    <span class="pd-index-text text-gray-400" x-show="!isThisActive">{{ $index + 1 }}</span>
                                    <svg class="pd-play-icon w-5 h-5" x-show="!isThisPlaying" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    <svg class="pd-play-icon pd-play-icon--active" x-show="isThisPlaying" x-cloak fill="currentColor" viewBox="0 0 24 24"><path d="M6 5h4v14H6zm8 0h4v14h-4z" /></svg>
                                </td>
                                <td class="py-3">
                                    <div class="pd-cell-title flex items-center gap-4">
                                        <img src="{{ $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" class="pd-mini-cover">
                                        <div class="flex flex-col">
                                            <span class="pd-song-name" :class="{ 'pd-song-name--active': isThisActive }">{{ $podcast->titulo }}</span>
                                            <span class="pd-author-name">{{ $podcast->autor?->nombre ?? 'Autor desconocido' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-gray-500">{{ $podcast->album ?? 'Recursos' }}</td>
                                <td class="text-gray-400 text-sm">Hace 2 días</td>
                                <td class="text-right text-gray-500 font-semibold">{{ $podcast->duracion_minutos }}:00</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 60px; color: #9ca3af; background: #f9fafb; border-radius: 16px;">
                <p style="font-size: 18px; font-weight: 600;">No hay audios disponibles</p>
                <p style="font-size: 14px;">Estamos trabajando para traerte contenido nuevo pronto.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>