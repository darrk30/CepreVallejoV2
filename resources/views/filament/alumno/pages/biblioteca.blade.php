@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biblioteca.css') }}">
@endpush
<x-filament-panels::page>
    <div class="bv">
        {{-- MASTHEAD ── --}}
        <div class="bv-masthead">
            <div class="bv-masthead-left">
                <p class="bv-masthead-eyebrow">Cepre Vallejo · Recursos Académicos</p>
                <h1 class="bv-masthead-title">Biblioteca <em>Virtual</em></h1>
                <p class="bv-masthead-count" id="bv-count">&nbsp;</p>
            </div>
            <div class="bv-masthead-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        {{-- TOOLBAR ── --}}
        <div class="bv-toolbar">
            <div class="bv-search-wrap">
                <svg class="bv-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" class="bv-search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por título, autor…">
            </div>

            <div class="bv-chips">
                <button class="bv-chip {{ is_null($areaId) ? 'active' : '' }}"
                    wire:click="$set('areaId', null)">Todos</button>
                @foreach ($this->areas as $area)
                    <button class="bv-chip {{ $areaId == $area->id ? 'active' : '' }}"
                        wire:click="$set('areaId', {{ $area->id }})">
                        {{ $area->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- FAVORITOS ── --}}
        @if ($this->favoritos->count() > 0 && !$search && !$areaId)
            <div class="bv-section-head">
                <h2 class="bv-section-title">❤️ Mis favoritos</h2>
                <span class="bv-section-line"></span>
                <span class="bv-section-count">{{ $this->favoritos->count() }} libro{{ $this->favoritos->count() !== 1 ? 's' : '' }}</span>
            </div>

            <div class="bv-grid">
                @foreach ($this->favoritos as $libro)
                    @include('filament.alumno.pages.parts.libro-card', [
                        'libro'      => $libro,
                        'isFavorite' => true,
                    ])
                @endforeach
            </div>

            <hr class="bv-separator">
        @endif

        {{-- BIBLIOTECA GENERAL ── --}}
        <div class="bv-section-head">
            <h2 class="bv-section-title">📚 Biblioteca General</h2>
            <span class="bv-section-line"></span>
            <span class="bv-section-count" id="bv-general-count">{{ $this->libros->count() }} títulos</span>
        </div>

        <div class="bv-grid">
            @forelse($this->libros as $libro)
                @include('filament.alumno.pages.parts.libro-card', [
                    'libro'      => $libro,
                    'isFavorite' => $libro->isFavoritedBy(auth()->user()),
                ])
            @empty
                <div class="bv-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <h3>Sin resultados</h3>
                    <p>No encontramos libros que coincidan con tu búsqueda.</p>
                </div>
            @endforelse
        </div>

    </div>

    <script>
        function actualizarContador() {
            const count = document.querySelectorAll('.libro-card').length;
            const el = document.getElementById('bv-count');
            if (el) el.textContent = `${count} título${count !== 1 ? 's' : ''} disponible${count !== 1 ? 's' : ''}`;
        }
        document.addEventListener('DOMContentLoaded', actualizarContador);
        document.addEventListener('livewire:updated', actualizarContador);
    </script>
</x-filament-panels::page>