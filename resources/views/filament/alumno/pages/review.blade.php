<x-filament-panels::page>
    <style>
        .fi-header-actions,
        .fi-header-heading-actions,
        .fi-resource-header .fi-ac,
        header .fi-ac {
            display: none !important;
        }
    </style>

    <div class="font-handlee pb-[60px] text-[#0c0b1a] dark:text-[#edecf8]">


        {{-- MASTHEAD ── --}}
        <div
            class="mb-7 flex flex-wrap items-center justify-between gap-5 border-b border-[#6366f1]/14 pb-2.5 dark:border-[#818cf8]/12">
            <div class="min-w-0 flex-1">
                <p
                    class="m-0 mb-1.5 text-[0.62rem] font-bold uppercase tracking-[0.15em] text-[#5b5ef4] dark:text-[#818cf8]">
                    Cepre Vallejo · Recursos Académicos</p>
                <h1
                    class="m-0 text-[clamp(1.8rem,4vw,2.8rem)] font-extrabold leading-[1.1] tracking-[-0.02em] text-[#0c0b1a] dark:text-[#edecf8]">
                    Tu <em class="italic text-[#5b5ef4] dark:text-[#818cf8]">Comentario</em>
                </h1>
                <p class="mt-2 text-[0.72rem] font-semibold text-[#8b88b0] dark:text-[#636086]">
                    Comparte tu opinión
                </p>
            </div>
        </div>

        {{-- FORMULARIO ── --}}
        <div
            class="rounded-[18px] border border-[#6366f1]/14 bg-white p-6 shadow-[0_1px_4px_rgba(79,70,229,0.07),0_1px_2px_rgba(0,0,0,0.04)] dark:border-[#818cf8]/12 dark:bg-[#151424]">

            <form
                wire:submit="{{ $this instanceof \App\Filament\Alumno\Resources\Reviews\Pages\EditReview ? 'save' : 'create' }}">
                {{ $this->form }}

                <div class="mt-5 flex items-center justify-between gap-2">
                    @if ($this instanceof \App\Filament\Alumno\Resources\Reviews\Pages\EditReview)
                        <button type="button" wire:click="mountAction('delete')"
                            class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-[0.78rem] font-bold text-red-500 transition-colors hover:bg-red-500/10">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Eliminar
                        </button>
                    @else
                        <span></span>
                    @endif

                    <div class="flex gap-2">
                        @foreach ($this->getFormActions() as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

    </div>
</x-filament-panels::page>
