{{-- Migrado a Tailwind. Si algo falla, descomenta el <style> de abajo (git
     historial) y quita las clases Tailwind del <div class="libro-card">. --}}
<div class="group libro-card relative flex h-full cursor-pointer flex-col overflow-hidden rounded-xl border border-[#6366f1]/14 bg-white shadow-[0_1px_4px_rgba(79,70,229,0.07),0_1px_2px_rgba(0,0,0,0.04)] transition-[transform,box-shadow,border-color] duration-[350ms] ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-[5px] hover:-rotate-[0.25deg] hover:border-[#5b5ef4]/25 hover:shadow-[0_16px_40px_rgba(79,70,229,0.13),0_4px_12px_rgba(79,70,229,0.07)] dark:border-[#818cf8]/12 dark:bg-[#151424]">

    {{-- Enlace que cubre toda la tarjeta --}}
    <a href="{{ $libro->url }}" target="_blank" rel="noopener" class="absolute inset-0 z-[1]"
        title="Leer {{ $libro->nombre }}">
    </a>

    {{-- Portada --}}
    <div class="relative aspect-[3/4] overflow-hidden bg-[#eeecfb] dark:bg-[#221f3a]">
        @if ($libro->image_path)
            <img src="{{ asset('storage/' . $libro->image_path) }}" alt="{{ $libro->nombre }}" loading="lazy"
                class="block h-full w-full object-cover transition-transform duration-[550ms] ease-[cubic-bezier(0.4,0,0.2,1)] group-hover:scale-[1.07]">
        @else
            <div class="flex h-full w-full flex-col items-center justify-center gap-2.5 bg-[#eeecfb] font-handlee text-[0.68rem] text-[#8b88b0] dark:bg-[#221f3a] dark:text-[#636086]">
                <svg class="h-[34px] w-[34px] text-[#5b5ef4] opacity-30 dark:text-[#818cf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Sin portada</span>
            </div>
        @endif

        {{-- Botón favorito: Usamos .stop para que el clic no active el enlace del libro --}}
        <button class="absolute top-[10px] right-[10px] z-10 flex h-[34px] w-[34px] items-center justify-center rounded-[9px] border-none bg-white/90 p-0 shadow-[0_4px_12px_rgba(0,0,0,0.1)] backdrop-blur-[8px] transition-transform duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-[1.15] dark:bg-[#1e1c37]/90"
            wire:click.stop="toggleFavorite({{ $libro->id }})" type="button">
            <svg class="h-[17px] w-[17px]" fill="{{ $isFavorite ? '#dc2626' : 'none' }}" stroke="{{ $isFavorite ? '#dc2626' : '#8b88b0' }}"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>
    </div>

    {{-- Info --}}
    <div class="flex flex-1 flex-col gap-1.5 p-[15px]">
        <span class="w-fit rounded-md border border-[#5b5ef4]/25 bg-[#5b5ef4]/7 px-[9px] py-[3px] text-[0.6rem] font-extrabold tracking-[0.08em] text-[#5b5ef4] uppercase dark:border-[#818cf8]/25 dark:bg-[#818cf8]/8 dark:text-[#818cf8]">{{ $libro->area?->nombre ?? 'General' }}</span>
        <h3 class="m-0 line-clamp-2 font-handlee text-base leading-[1.3] font-bold tracking-[-0.01em] text-[#0c0b1a] dark:text-[#edecf8]">{{ $libro->nombre }}</h3>
        <p class="mt-auto pt-2 text-[0.75rem] text-[#8b88b0] italic dark:text-[#636086]">{{ $libro->autor ?? 'Autor Institucional' }}</p>
    </div>

</div>
