{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las
     clases Tailwind del <div class="vp-root">) para volver al CSS clásico. --}}
{{--
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reproductor-video.css') }}">
@endpush
--}}
<x-filament-panels::page>
    <div class="pb-12 font-handlee text-[#0c0b1a] dark:text-[#edecf8]">
        <div class="grid grid-cols-1 gap-7 lg:grid-cols-[1fr_380px]">

            {{-- ══ REPRODUCTOR + INFO ══ --}}
            <div>
                <div class="relative h-0 overflow-hidden rounded-[14px] bg-black pb-[56.25%] shadow-[0_12px_40px_rgba(79,70,229,0.13),0_4px_12px_rgba(0,0,0,0.07)] dark:shadow-[0_12px_40px_rgba(0,0,0,0.5)]">
                    <iframe src="{{ $this->getEmbedUrl() }}" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                        class="absolute inset-0 h-full"
                        style="width: 100%; aspect-ratio: 16/9; border-radius: 12px; border: none;">
                    </iframe>
                </div>

                <div class="mt-5 rounded-[14px] border border-[#6366f1]/14 bg-white px-6 py-[22px] shadow-[0_1px_4px_rgba(79,70,229,0.07),0_1px_2px_rgba(0,0,0,0.04)] dark:border-[#818cf8]/12 dark:bg-[#151424]">
                    <div class="mb-3 inline-flex items-center gap-[5px] rounded-full border border-[#5b5ef4]/25 bg-[#5b5ef4]/7 px-2.5 py-[3px] text-[0.62rem] font-bold tracking-[0.1em] text-[#5b5ef4] uppercase dark:border-[#818cf8]/25 dark:bg-[#818cf8]/8 dark:text-[#818cf8]">
                        <svg class="h-[10px] w-[10px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                        </svg>
                        {{ $video->area?->nombre ?? 'General' }}
                    </div>

                    <h1 class="m-0 mb-2.5 text-[clamp(1.3rem,3vw,1.9rem)] leading-[1.2] font-extrabold tracking-[-0.02em] text-[#0c0b1a] dark:text-[#edecf8]">{{ $video->titulo }}</h1>

                    <div class="mb-4 flex flex-wrap items-center gap-4 border-b border-[#6366f1]/14 pb-4 dark:border-[#818cf8]/12">
                        @if ($video->autor)
                            <span class="inline-flex items-center gap-[5px] text-[0.78rem] font-medium text-[#8b88b0] dark:text-[#636086]">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $video->autor }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-[5px] text-[0.78rem] font-medium text-[#8b88b0] dark:text-[#636086]">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 013.741-1.234"/></svg>
                            Institución Cepre Vallejo
                        </span>
                    </div>

                    @if ($video->descripcion)
                        <div class="text-[0.88rem] leading-[1.7] text-[#3b3866] dark:text-[#a9a6cc]">{{ $video->descripcion }}</div>
                    @endif
                </div>
            </div>

            {{-- ══ SIDEBAR RECOMENDADOS ══ --}}
            <div class="flex flex-col gap-0">
                <h2 class="after:h-px after:flex-1 after:bg-[#6366f1]/14 m-0 mb-3.5 flex items-center gap-2.5 text-[1.1rem] font-bold text-[#0c0b1a] after:content-[''] dark:text-[#edecf8] dark:after:bg-[#818cf8]/12">A continuación</h2>

                @foreach($this->recommended_videos as $i => $rec)
                    @php
                        $recThumb = $rec->image_path ? asset('storage/' . $rec->image_path) : null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $rec->url, $match)) {
                            $recThumb = "https://img.youtube.com/vi/{$match[1]}/mqdefault.jpg";
                        }
                    @endphp

                    @if ($i > 0)
                        <div class="my-1 mx-2.5 h-px bg-[#6366f1]/14 dark:bg-[#818cf8]/12"></div>
                    @endif

                    <a href="{{ \App\Filament\Alumno\Pages\ReproductorVideo::getUrl(['videoSlug' => $rec->slug]) }}"
                       class="group flex gap-3 rounded-[14px] border border-transparent p-2.5 no-underline transition-[background-color,transform,box-shadow] duration-150 hover:translate-x-[3px] hover:border-[#6366f1]/14 hover:bg-[#f9f8ff] hover:shadow-[0_1px_4px_rgba(79,70,229,0.07),0_1px_2px_rgba(0,0,0,0.04)] dark:hover:border-[#818cf8]/12 dark:hover:bg-[#1b1a2e]">
                        <div class="relative aspect-video w-[140px] shrink-0 overflow-hidden rounded-[9px] bg-[#eeecfb] dark:bg-[#221f3a]">
                            @if ($recThumb)
                                <img src="{{ $recThumb }}" class="block h-full w-full object-cover transition-transform duration-400 group-hover:scale-[1.06]" alt="{{ $rec->titulo }}" loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-[#eeecfb] dark:bg-[#221f3a]">
                                    <svg class="h-5 w-5 text-[#8b88b0] dark:text-[#636086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center bg-[#0b0a1a]/35 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                <div class="flex h-[30px] w-[30px] items-center justify-center rounded-full bg-[#5b5ef4] shadow-[0_4px_12px_rgba(91,94,244,0.18)] dark:bg-[#818cf8] dark:shadow-[0_4px_12px_rgba(129,140,248,0.22)]">
                                    <svg class="ml-0.5 h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 2.691l11 7.309-11 7.309V2.691z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col gap-1 pt-0.5">
                            <span class="text-[0.58rem] font-bold tracking-[0.1em] text-[#5b5ef4] uppercase dark:text-[#818cf8]">{{ $rec->area?->nombre ?? 'General' }}</span>
                            <h4 class="m-0 line-clamp-2 text-[0.84rem] leading-[1.3] font-bold text-[#0c0b1a] dark:text-[#edecf8]">{{ $rec->titulo }}</h4>
                            @if ($rec->descripcion)
                                <p class="m-0 line-clamp-2 text-[0.73rem] leading-[1.45] text-[#8b88b0] dark:text-[#636086]">{{ $rec->descripcion }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-filament-panels::page>
