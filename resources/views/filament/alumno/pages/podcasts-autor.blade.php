{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las
     clases Tailwind del <div class="podcasts-wrapper">) para volver al CSS clásico. --}}
{{--
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/podcatsautor.css') }}?v={{ filemtime(public_path('css/podcatsautor.css')) }}">
@endpush
--}}
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

    <div class="font-nunito text-gray-700 dark:text-[#d7d9ea]" x-data x-init=" $store.podcastPlayer.setPlaylist({{ json_encode($playlistData) }}) ">

        <!-- Banner del Autor -->
        <div class="mb-1 flex items-center gap-5 rounded-3xl border border-violet-600/10 bg-[linear-gradient(90deg,#ede9fe_0%,#ffffff_100%)] px-10 py-[30px] shadow-[0_10px_25px_rgba(0,0,0,0.05)] dark:bg-[linear-gradient(90deg,#2b2850_0%,#1a1c2c_100%)]">
            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border-[3px] border-white shadow-[0_6px_16px_rgba(124,58,237,0.15)] sm:h-[130px] sm:w-[130px] sm:rounded-full sm:border-4 dark:border-[#1a1c2c]">
                <img src="{{ $autor->imagen ? asset('storage/' . $autor->imagen) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" alt="{{ $autor->nombre }}" class="h-full w-full object-cover">
            </div>
            <div class="flex flex-1 flex-col justify-center">
                <p class="m-0 mb-1 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 sm:text-[13px] dark:text-[#9599b8]">Autor</p>
                <h1 class="m-0 font-handlee text-[26px] font-bold leading-[1.1] text-gray-800 [text-shadow:1px_1px_2px_rgba(255,255,255,0.8)] sm:text-[48px] dark:text-[#f1f2fb]">{{ $autor->nombre }}</h1>
                <p class="mt-1 text-[13px] font-semibold text-violet-600 sm:mt-2 sm:text-sm">{{ $podcasts->count() }} {{ Str::plural('podcast', $podcasts->count()) }}</p>
            </div>
        </div>

        <!-- Separador -->
        <div class="my-8 h-px w-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-[#33364f] dark:via-[#40435f] dark:to-[#33364f]"></div>

        <!-- Título de subsección: Episodios -->
        <h2 class="mb-4 text-xs font-extrabold uppercase tracking-[0.08em] text-violet-600">Podcasts de {{ $autor->nombre }}</h2>

        <!-- Tabla de Podcasts (Estilo Claro) -->
        <div class="overflow-x-auto">
            <table class="mt-4 w-full border-collapse">
                <thead>
                    <tr>
                        <th class="w-[50px] border-b border-gray-200 px-4 py-3 text-center text-xs uppercase tracking-wider text-gray-400 dark:border-[#33364f] dark:text-[#9599b8]">#</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-400 dark:border-[#33364f] dark:text-[#9599b8]">Título</th>
                        <th class="hidden border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-400 sm:table-cell dark:border-[#33364f] dark:text-[#9599b8]">Álbum</th>
                        <th class="hidden border-b border-gray-200 px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-400 sm:table-cell dark:border-[#33364f] dark:text-[#9599b8]">Fecha</th>
                        <th class="hidden border-b border-gray-200 px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-400 sm:table-cell dark:border-[#33364f] dark:text-[#9599b8]">Duración</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($podcasts as $index => $podcast)
                    <tr
                        class="group cursor-pointer transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-[#23253c]"
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
                        :class="{ 'bg-violet-50 dark:bg-[#2b2850]': isThisActive }"
                        @click="handlePlay()">
                        <!-- Índice fijo para evitar saltos -->
                        <td class="relative h-[60px] w-[50px] border-b border-gray-100 px-2 py-2.5 text-center dark:border-[#262840]">
                            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-gray-400 transition-opacity duration-200 group-hover:opacity-0" x-show="!isThisActive">{{ $index + 1 }}</span>

                            <svg class="absolute left-1/2 top-1/2 h-5 w-5 -translate-x-1/2 -translate-y-1/2 text-violet-600 opacity-0 transition-opacity duration-200 group-hover:opacity-100" x-show="!isThisPlaying" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <svg class="absolute left-1/2 top-1/2 h-5 w-5 -translate-x-1/2 -translate-y-1/2 text-violet-600 opacity-100" x-show="isThisPlaying" x-cloak fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 5h4v14H6zm8 0h4v14h-4z" />
                            </svg>
                        </td>

                        <!-- Título y Autor -->
                        <td class="border-b border-gray-100 px-4 py-3 dark:border-[#262840]">
                            <div class="flex min-w-0 items-center gap-4">
                                <img src="{{ $podcast->imagen_portada ? asset('storage/' . $podcast->imagen_portada) : 'https://thumbs.dreamstime.com/b/ilustraci%C3%B3n-de-avatar-empresario-retrato-usuario-dibujos-animados-icono-perfil-simple-un-l%C3%ADder-empresarial-vectorial-276189185.jpg' }}" alt="{{ $autor->nombre }}" class="h-10 w-10 shrink-0 rounded-md object-cover sm:h-12 sm:w-12">
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <span class="block truncate font-handlee text-[17px] font-bold text-gray-800 sm:overflow-visible sm:text-clip sm:whitespace-normal dark:text-[#f1f2fb]"
                                          :class="{ 'text-violet-600': isThisActive }">{{ $podcast->titulo }}</span>
                                    <span class="block truncate text-[13px] text-gray-400 sm:overflow-visible sm:text-clip sm:whitespace-normal dark:text-[#9599b8]">{{ $autor->nombre }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Resto de columnas -->
                        <td class="hidden border-b border-gray-100 px-4 py-3 text-gray-500 sm:table-cell dark:border-[#262840]">{{ $podcast->album ?? 'Recursos' }}</td>
                        <td class="hidden border-b border-gray-100 px-4 py-3 text-sm text-gray-400 sm:table-cell dark:border-[#262840]">Hace 2 días</td>
                        <td class="hidden border-b border-gray-100 px-4 py-3 text-right font-semibold text-gray-500 sm:table-cell dark:border-[#262840]">{{ $podcast->duracion_minutos }}:00</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400">Este autor aún no tiene podcasts.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
