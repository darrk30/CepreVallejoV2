

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/podcast-swiper.js') }}?v={{ filemtime(public_path('js/podcast-swiper.js')) }}" defer></script>
@endpush

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

    <div class="font-handlee text-gray-700 dark:text-[#d7d9ea]" x-data x-init=" $store.podcastPlayer.setPlaylist({{ json_encode($playlistData) }}) ">

        <div class="mb-3.5">
            <h1 class="m-0 text-3xl font-bold text-gray-800 dark:text-[#f1f2fb]">Nuestros Podcasts</h1>
            <p class="mt-1 text-sm text-gray-400 dark:text-[#9599b8]">Escucha el contenido preparado para ti</p>
        </div>

        <div class="my-2.5 h-px w-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-[#33364f] dark:via-[#40435f] dark:to-[#33364f]"></div>

        <!-- Sección de Autores -->
        @if($this->autores->isNotEmpty())
            <h2 class="mb-4 text-xs font-extrabold uppercase tracking-widest text-violet-600">Autores</h2>
            <div class="swiper authorsSwiper mb-12 w-full max-w-full overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach($this->autores as $autor)
                        {{-- !w-auto/!min-w-[...] con !important: Swiper trae su propio CSS sin
                             @layer, que gana por spec de cascada a cualquier utilidad de Tailwind
                             (van dentro de un layer) con la misma especificidad, sin importar el
                             orden de carga. --}}
                        <div class="swiper-slide !w-auto !min-w-[90px] px-1.5 sm:!min-w-[140px] sm:px-2.5">
                            <a href="{{ \App\Filament\Alumno\Pages\PodcastsAutor::getUrl(['autor_id' => $autor->id]) }}"
                               class="group flex flex-col items-center no-underline transition-transform duration-200 hover:scale-105"
                               onclick="window.Livewire.navigate(this.href); return false;">
                                <div class="h-[70px] w-[70px] overflow-hidden rounded-full border-2 border-gray-200 transition-colors duration-200 group-hover:border-violet-600 sm:h-[100px] sm:w-[100px] sm:border-[3px] dark:border-[#33364f]">
                                    <img src="{{ $autor->imagen ? asset('storage/'.$autor->imagen) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" alt="{{ $autor->nombre }}" class="h-full w-full object-cover">
                                </div>
                                <span class="mt-2 block px-1 text-center text-xs font-semibold leading-tight text-gray-700 sm:mt-3 sm:text-base dark:text-[#d7d9ea]">{{ $autor->nombre }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mb-8 rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center text-gray-400 dark:border-[#33364f] dark:text-[#9599b8]">
                <p>Aún no hay autores registrados.</p>
            </div>
        @endif

        <div class="my-2.5 h-px w-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-[#33364f] dark:via-[#40435f] dark:to-[#33364f]"></div>

        <!-- Sección de Audios -->
        <h2 class="mb-4 text-xs font-extrabold uppercase tracking-widest text-violet-600">Audios</h2>

        @if($this->podcasts->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="mt-4 w-full table-fixed border-collapse">
                    <thead>
                        <tr>
                            <th class="w-[50px] border-b border-gray-200 px-4 py-3 text-center text-xs uppercase tracking-wide text-gray-400 dark:border-[#33364f] dark:text-[#9599b8]">#</th>
                            <th class="border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-[#33364f] dark:text-[#9599b8]">Título</th>
                            <th class="hidden border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wide text-gray-400 sm:table-cell sm:w-[20%] dark:border-[#33364f] dark:text-[#9599b8]">Álbum</th>
                            <th class="hidden border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wide text-gray-400 sm:table-cell sm:w-[16%] dark:border-[#33364f] dark:text-[#9599b8]">Fecha</th>
                            <th class="hidden border-b border-gray-200 px-4 py-3 text-right text-xs uppercase tracking-wide text-gray-400 sm:table-cell sm:w-[14%] dark:border-[#33364f] dark:text-[#9599b8]">Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->podcasts as $index => $podcast)
                            <tr class="group cursor-pointer transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-[#23253c]"
                                x-data="{ podcastData: {{ json_encode(['id' => $podcast->id, 'title' => $podcast->titulo, 'cover' => $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : null, 'src' => $podcast->stream_url]) }}, get isThisPlaying() { return $store.podcastPlayer.current?.id === this.podcastData.id && $store.podcastPlayer.isPlaying; }, get isThisActive() { return $store.podcastPlayer.current?.id === this.podcastData.id; }, handlePlay() { if (this.isThisActive) { $store.podcastPlayer.toggle(); } else { $store.podcastPlayer.play(this.podcastData); } } }"
                                :class="{ 'bg-violet-50 dark:bg-[#2b2850]': isThisActive }"
                                @click="handlePlay()">
                                <td class="relative h-[60px] w-[50px] border-b border-gray-100 px-2 py-2.5 text-center dark:border-[#262840]">
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-gray-400 transition-opacity duration-200 group-hover:opacity-0" x-show="!isThisActive">{{ $index + 1 }}</span>
                                    <svg class="absolute left-1/2 top-1/2 h-5 w-5 -translate-x-1/2 -translate-y-1/2 text-violet-600 opacity-0 transition-opacity duration-200 group-hover:opacity-100" x-show="!isThisPlaying" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                    <svg class="absolute left-1/2 top-1/2 h-5 w-5 -translate-x-1/2 -translate-y-1/2 text-violet-600 opacity-100" x-show="isThisPlaying" x-cloak fill="currentColor" viewBox="0 0 24 24"><path d="M6 5h4v14H6zm8 0h4v14h-4z" /></svg>
                                </td>
                                <td class="border-b border-gray-100 px-4 py-3 dark:border-[#262840]">
                                    <div class="flex min-w-0 items-center gap-2.5 sm:gap-4">
                                        <img src="{{ $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" class="h-10 w-10 shrink-0 rounded-md object-cover sm:h-12 sm:w-12">
                                        <div class="flex min-w-0 flex-1 flex-col">
                                            <span class="block whitespace-normal break-words text-[15px] font-bold text-gray-800 group-hover:text-violet-600 sm:overflow-hidden sm:text-ellipsis sm:whitespace-nowrap sm:text-[17px] dark:text-[#f1f2fb]"
                                                  :class="{ 'text-violet-600': isThisActive }">{{ $podcast->titulo }}</span>
                                            <span class="-mt-0.5 block whitespace-normal break-words text-sm text-gray-400 sm:overflow-hidden sm:text-ellipsis sm:whitespace-nowrap dark:text-[#9599b8]">{{ $podcast->autor?->nombre ?? 'Autor desconocido' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden border-b border-gray-100 px-4 py-3 text-gray-500 sm:table-cell dark:border-[#262840]">{{ $podcast->album ?? 'Recursos' }}</td>
                                <td class="hidden border-b border-gray-100 px-4 py-3 text-sm text-gray-400 sm:table-cell dark:border-[#262840]">Hace 2 días</td>
                                <td class="hidden border-b border-gray-100 px-4 py-3 text-right font-semibold text-gray-500 sm:table-cell dark:border-[#262840]">{{ $podcast->duracion_minutos }}:00</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-2xl bg-gray-50 p-[60px] text-center text-gray-400 dark:bg-[#23253c] dark:text-[#9599b8]">
                <p class="text-lg font-semibold">No hay audios disponibles</p>
                <p class="text-sm">Estamos trabajando para traerte contenido nuevo pronto.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
