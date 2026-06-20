<!-- <link rel="stylesheet" href="{{ asset('css/podcatsautor.css') }}"> -->
<script src="{{ asset('js/podcast-player.js') }}" defer></script>
<x-filament-panels::page>
    @php
        // Traemos las propiedades del componente Livewire
        $podcasts = $this->podcasts;
        $autor = $this->autor; // <-- AÑADE ESTA LÍNEA AQUÍ

        // Preparamos la lista para enviarla a JavaScript de forma segura
        $playlistData = $podcasts->map(function($p) {
            return [
                'id' => $p->id,
                'title' => $p->titulo,
                'cover' => $p->imagen_portada ? asset('storage/' . $p->imagen_portada) : null,
                'src' => $p->stream_url,
            ];
        })->values();
    @endphp

    <div class="podcasts-wrapper" x-data x-init=" $store.podcastPlayer.setPlaylist({{ json_encode($playlistData) }}) ">

        <!-- Banner del Autor -->
        <div class="pd-autor-banner">
            <div class="pd-autor-banner-img">
                {{-- Ahora $autor ya existe y esto funcionará perfecto --}}
                <img src="{{ $autor->imagen ? asset('storage/' . $autor->imagen) : asset('img/default-user.png') }}" alt="{{ $autor->nombre }}">
            </div>
            <div class="pd-autor-banner-info">
                <p class="pd-autor-banner-eyebrow">Autor</p>
                <h1 class="pd-autor-banner-name">{{ $autor->nombre }}</h1>
                <p class="pd-autor-banner-count">{{ $podcasts->count() }} {{ Str::plural('podcast', $podcasts->count()) }}</p>
            </div>
        </div>

        <!-- Separador -->
        <div class="pd-divider"></div>

        <!-- Título de subsección: Episodios -->
        <h2 class="pd-section-subtitle">Podcasts de {{ $autor->nombre }}</h2>

        <!-- Tabla de Podcasts (Estilo Claro) -->
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
                    @forelse($podcasts as $index => $podcast)
                    <tr
                        class="pd-row"
                        x-data="{
                            podcastData: {{ json_encode([
                                'id' => $podcast->id,
                                'title' => $podcast->titulo,
                                'cover' => $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : null,
                                'src' => $podcast->stream_url,
                            ]) }},
                            get isThisPlaying() {
                                return $store.podcastPlayer.current?.id === this.podcastData.id && $store.podcastPlayer.isPlaying;
                            },
                            get isThisActive() {
                                return $store.podcastPlayer.current?.id === this.podcastData.id;
                            },
                            handlePlay() {
                                if (this.isThisActive) {
                                    $store.podcastPlayer.toggle();
                                } else {
                                    $store.podcastPlayer.play(this.podcastData);
                                }
                            }
                        }"
                        :class="{ 'pd-row--active': isThisActive }"
                        @click="handlePlay()">
                        <!-- Índice fijo para evitar saltos -->
                        <td class="pd-index-col">
                            <span class="pd-index-text text-gray-400" x-show="!isThisActive">{{ $index + 1 }}</span>

                            <svg class="pd-play-icon w-5 h-5" x-show="!isThisPlaying" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <svg class="pd-play-icon pd-play-icon--active" x-show="isThisPlaying" x-cloak fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 5h4v14H6zm8 0h4v14h-4z" />
                            </svg>
                        </td>

                        <!-- Título y Autor -->
                        <td class="py-3">
                            <div class="pd-cell-title flex items-center gap-4">
                                <img src="{{ $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : asset('img/default-cover.png') }}" class="pd-mini-cover">
                                <div class="flex flex-col">
                                    <span class="pd-song-name" :class="{ 'pd-song-name--active': isThisActive }">{{ $podcast->titulo }}</span>
                                    <span class="pd-author-name">{{ $autor->nombre }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Resto de columnas -->
                        <td class="text-gray-500">{{ $podcast->album ?? 'Recursos' }}</td>
                        <td class="text-gray-400 text-sm">Hace 2 días</td>
                        <td class="text-right text-gray-500 font-semibold">{{ $podcast->duracion_minutos }}:00</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-400">Este autor aún no tiene podcasts.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>