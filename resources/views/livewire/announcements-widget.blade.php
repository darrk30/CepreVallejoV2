@push('styles')
    <link rel="stylesheet" href="{{ asset('css/announcements-widget.css') }}">
@endpush
<x-filament-widgets::widget>
    @php
        $anuncios = $this->getAnuncios();
    @endphp

    @if($anuncios->count() > 0)
        <div class="banner-vertical-wrapper">
            @foreach($anuncios as $anuncio)
                <div class="card-banner card--{{ $anuncio->tipo }}">
                    
                    {{-- 1. Imagen Superior (Full Width) --}}
                    @if($anuncio->image_path)
                        <div class="card-image-top">
                            <img src="{{ Storage::url($anuncio->image_path) }}" alt="Banner">
                            <div class="card-overlay"></div>
                        </div>
                    @endif

                    {{-- 2. Cuerpo de la Carta --}}
                    <div class="card-main">
                        <div class="card-stripe"></div>
                        
                        <div class="card-info">
                            <div class="card-meta">
                                <span class="card-tag">{{ $anuncio->titulo }}</span>
                                <span class="card-time">{{ $anuncio->fecha_inicio?->diffForHumans() }}</span>
                            </div>
                            
                            <div class="card-text">
                                {!! $anuncio->contenido !!}
                            </div>
                        </div>

                        {{-- 3. Botón de Acción --}}
                        @if($anuncio->url)
                            <div class="card-actions">
                                <a href="{{ $anuncio->url }}" target="_blank" class="card-btn">
                                    VER MÁS
                                    <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>