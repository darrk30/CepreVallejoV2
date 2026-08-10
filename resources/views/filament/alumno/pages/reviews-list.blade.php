<x-filament-panels::page>
    <div class="font-handlee pb-[60px] text-[#0c0b1a] dark:text-[#edecf8]" x-data="{ modalAbierto: false, modalTitulo: '', modalTexto: '', modalFecha: '' }"
        @keydown.escape.window="modalAbierto = false">

        {{-- MASTHEAD ── --}}
        <div
            class="mb-7 flex flex-wrap items-center justify-between gap-5 border-b border-[#6366f1]/14 pb-2.5 dark:border-[#818cf8]/12">
            <div class="min-w-0 flex-1">
                <p
                    class="m-0 mb-1.5 text-[0.62rem] font-bold uppercase tracking-[0.15em] text-[#5b5ef4] dark:text-[#818cf8]">
                    Cepre Vallejo · Recursos Académicos</p>
                <h1
                    class="m-0 text-[clamp(1.8rem,4vw,2.8rem)] font-extrabold leading-[1.1] tracking-[-0.02em] text-[#0c0b1a] dark:text-[#edecf8]">
                    Mis <em class="italic text-[#5b5ef4] dark:text-[#818cf8]">Comentarios</em></h1>
                <p class="mt-2 text-[0.72rem] font-semibold text-[#8b88b0] dark:text-[#636086]">
                    {{ $this->getReviews()->total() }} comentario{{ $this->getReviews()->total() !== 1 ? 's' : '' }}
                    publicado{{ $this->getReviews()->total() !== 1 ? 's' : '' }}
                </p>
            </div>
            @can('create_review')
                <a href="{{ \App\Filament\Alumno\Resources\Reviews\ReviewResource::getUrl('create') }}" wire:navigate
                    class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#5b5ef4] px-5 py-2.5 text-[0.8rem] font-bold text-white no-underline shadow-[0_4px_12px_rgba(91,94,244,0.18)] transition-transform duration-150 hover:translate-x-[1px] hover:bg-[#4b4ee0] dark:bg-[#818cf8] dark:shadow-[0_4px_12px_rgba(129,140,248,0.22)] dark:hover:bg-[#6f78f0]">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Crear Comentario
                </a>
            @endcan
        </div>

        {{-- GRILLA DE TARJETAS ── --}}
        <div class="grid grid-cols-2 gap-4 min-[560px]:grid-cols-3 min-[900px]:grid-cols-4 min-[1200px]:grid-cols-5">
            @forelse ($this->getReviews() as $index => $review)
                @php
                    $mensajeTexto = strip_tags($review->mensaje);
                    $esLargo = mb_strlen($mensajeTexto) > 140;
                    $mensajeCorto = $esLargo ? mb_substr($mensajeTexto, 0, 140) . '…' : $mensajeTexto;
                    $numero = $index + 1 + ($this->getReviews()->currentPage() - 1) * $this->getReviews()->perPage();
                @endphp
                <div wire:key="review-{{ $review->id }}"
                    class="group relative flex flex-col overflow-hidden rounded-[18px] border border-[#6366f1]/12 bg-white p-6 shadow-[0_2px_8px_rgba(79,70,229,0.06)] transition-all duration-200 hover:border-[#5b5ef4]/25 hover:shadow-[0_10px_24px_rgba(79,70,229,0.14)] dark:border-[#818cf8]/10 dark:bg-[#151424] dark:hover:border-[#818cf8]/30">
                    {{-- acento superior --}}
                    <div
                        class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-[#5b5ef4] to-[#8b8ef8] opacity-70 dark:from-[#818cf8] dark:to-[#a5abfb]">
                    </div>

                    <div class="mb-1.5 flex items-start justify-between gap-2">
                        <h3
                            class="m-0 line-clamp-1 text-[1.25rem] font-bold leading-[1.3] text-[#0c0b1a] dark:text-[#edecf8]">
                            COMENTARIO N°{{ $numero }}
                        </h3>
                    </div>

                    <p class="m-0 mb-3 text-[0.90rem] italic leading-[1.70] text-[#5b5875] dark:text-[#9d9ac2]">
                        {{ $mensajeCorto }}
                        @if ($esLargo)
                            <button type="button"
                                @click="modalAbierto = true; modalTitulo = 'COMENTARIO N°{{ $numero }}'; modalTexto = {{ Illuminate\Support\Js::from($mensajeTexto) }}; modalFecha = '{{ $review->fecha_hora->format('d M, Y') }}'"
                                class="ml-1 inline-block font-bold not-italic text-[#5b5ef4] hover:underline dark:text-[#818cf8]">Leer
                                más</button>
                        @endif
                    </p>

                    <div class="mt-auto flex items-center justify-between pt-1">
                        <span
                            class="text-[0.6rem] font-medium text-[#a5a2c4] dark:text-[#54506e]">{{ $review->fecha_hora->format('d M, Y') }}</span>
                        <div
                            class="flex items-center gap-1 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                            @can('update_review')
                                <a href="{{ \App\Filament\Alumno\Resources\Reviews\ReviewResource::getUrl('edit', ['record' => $review]) }}"
                                    wire:navigate
                                    class="flex h-6 w-6 items-center justify-center rounded-full text-[#5b5ef4] transition-colors hover:bg-[#5b5ef4]/10 dark:text-[#818cf8]">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
                                    </svg>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full rounded-[16px] border-[1.5px] border-dashed border-[#6366f1]/25 bg-[#f9f8ff] px-8 py-[72px] text-center dark:border-[#818cf8]/25 dark:bg-[#1b1a2e]">
                    <h3 class="m-0 mb-1 text-[0.9rem] font-bold text-[#3b3866] dark:text-[#a9a6cc]">Aún no tienes
                        comentarios</h3>
                    <p class="m-0 text-[0.75rem] text-[#8b88b0] dark:text-[#636086]">Publica tu primer comentario con el
                        botón de arriba.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN ── --}}
        @if ($this->getReviews()->hasPages())
            <div class="mt-9 flex justify-center">
                {{ $this->getReviews()->links() }}
            </div>
        @endif

        {{-- MODAL ── --}}
        <template x-teleport="body">
            <div x-show="modalAbierto" x-cloak
                style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; margin: 0; z-index: 9000; background-color: rgba(12,11,26,0.6); backdrop-filter: blur(2px);"
                @click.self="modalAbierto = false">

                <div x-show="modalAbierto" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 560px; max-width: 90vw; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden; border-radius: 20px; background: white; box-shadow: 0 30px 70px rgba(12,11,26,0.35); z-index: 9001;"
                    class="dark:!bg-[#151424]">

                    {{-- HEADER --}}
                    <div
                        style="padding: 24px 28px 16px 28px; border-bottom: 1px solid rgba(99,102,241,0.12); display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-shrink: 0;">
                        <div style="min-width: 0;">
                            <p
                                class="m-0 mb-1 text-[0.62rem] font-bold uppercase tracking-[0.15em] text-[#5b5ef4] dark:text-[#818cf8]">
                                Comentario completo</p>
                            <h3 class="m-0 truncate text-[1.1rem] font-extrabold leading-[1.3] text-[#0c0b1a] dark:text-[#edecf8]"
                                x-text="modalTitulo"></h3>
                        </div>
                        <button type="button" @click="modalAbierto = false"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[#8b88b0] transition-colors hover:bg-[#5b5ef4]/10 hover:text-[#5b5ef4] dark:text-[#636086] dark:hover:bg-[#818cf8]/10 dark:hover:text-[#818cf8]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- BODY --}}
                    <div style="padding: 20px 28px; overflow-y: auto; flex: 1 1 auto;">
                        <p class="m-0 whitespace-pre-line text-[0.88rem] italic leading-[1.8] text-[#3b3866] dark:text-[#c1bee0]"
                            x-text="modalTexto"></p>
                    </div>

                    {{-- FOOTER --}}
                    <div style="padding: 14px 28px; border-top: 1px solid rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-shrink: 0;"
                        class="bg-[#f9f8ff] dark:bg-[#1b1a2e]">
                        <span
                            class="inline-flex items-center gap-1.5 text-[0.68rem] font-semibold text-[#a5a2c4] dark:text-[#54506e]">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <span x-text="modalFecha"></span>
                        </span>
                        <button type="button" @click="modalAbierto = false"
                            class="rounded-full bg-[#5b5ef4] px-5 py-2 text-[0.75rem] font-bold text-white transition-colors hover:bg-[#4b4ee0] dark:bg-[#818cf8] dark:hover:bg-[#6f78f0]">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-filament-panels::page>
