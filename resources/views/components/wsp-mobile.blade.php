{{-- type: whatsapp | tiktok | link  |  show: mobile | desktop | all --}}
@props([
    'to',
    'type' => 'link',
    'message' => 'Hola, deseo realizar una consulta.',
    'bottom' => '25px',
    'color' => null,
    'label' => null,
    'show' => 'mobile',
])

@php
    $color = $color ?? match ($type) {
        'whatsapp' => '#25d366',
        'tiktok' => '#000000',
        default => '#2563eb',
    };

    $pulse = $type === 'tiktok' ? '#fe2c55' : $color;

    $url = match ($type) {
        'whatsapp' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $to) . '?text=' . urlencode($message),
        'tiktok' => Str::startsWith($to, 'http') ? $to : 'https://www.tiktok.com/@' . ltrim($to, '@'),
        default => $to,
    };

    $label = $label ?? match ($type) {
        'whatsapp' => 'Contactar por WhatsApp',
        'tiktok' => 'Visitar nuestro TikTok',
        default => 'Abrir enlace',
    };

    $showClass = match ($show) {
        'desktop' => 'fab-show-desktop',
        'all' => 'fab-show-all',
        default => 'fab-show-mobile',
    };
@endphp

@once
<style>
    .fab-container {
        position: fixed;
        right: 20px;
        z-index: 9999;
    }

    /* Visibilidad según dispositivo */
    .fab-container.fab-show-mobile {
        display: none; /* Oculto en escritorio */
    }

    .fab-container.fab-show-desktop {
        display: block; /* Oculto en móvil */
    }

    .fab-container.fab-show-all {
        display: block;
    }

    @media (max-width: 768px) {
        .fab-container.fab-show-mobile {
            display: block;
        }

        .fab-container.fab-show-desktop {
            display: none;
        }
    }

    /* El botón principal */
    .fab-button {
        position: relative;
        width: 62px;
        height: 62px;
        background-color: var(--fab-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        text-decoration: none;
        transition: all 0.3s ease;
        -webkit-tap-highlight-color: transparent;
        animation: fab-bounce 3s infinite ease-in-out;
    }

    /* Efecto glitch solo para TikTok */
    .fab-button.fab-tiktok {
        box-shadow: 3px 3px 0 #fe2c55, -3px -3px 0 #25f4ee, 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* El efecto de pulso (ondas) */
    .fab-button::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: var(--fab-pulse);
        border-radius: 50%;
        z-index: -1;
        animation: fab-pulse 2s infinite;
    }

    .fab-button svg {
        width: 32px;
        height: 32px;
    }

    .fab-button svg.fab-fill {
        fill: currentColor;
    }

    .fab-button svg.fab-stroke {
        fill: none;
        stroke: currentColor;
    }

    /* Estados de interacción */
    .fab-button:hover {
        transform: scale(1.08);
    }

    .fab-button:active {
        transform: scale(0.85);
        filter: brightness(0.85);
    }

    /* --- ANIMACIONES --- */
    @keyframes fab-pulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    @keyframes fab-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endonce

<div class="fab-container {{ $showClass }}"
     style="bottom: {{ $bottom }}; --fab-color: {{ $color }}; --fab-pulse: {{ $pulse }};">
    <a href="{{ $url }}"
       class="fab-button {{ $type === 'tiktok' ? 'fab-tiktok' : '' }}"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="{{ $label }}">

        @if ($type === 'whatsapp')
            <svg class="fab-fill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
            </svg>
        @elseif ($type === 'tiktok')
            <svg class="fab-fill" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
            </svg>
        @else
            <svg class="fab-stroke" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
            </svg>
        @endif
    </a>
</div>