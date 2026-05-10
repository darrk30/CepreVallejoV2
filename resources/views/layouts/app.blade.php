<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="icon" type="image/x-icon" href="{{ asset('img/cepreicono.ico') }}">
    <link rel="shortcut icon" href="{{ asset('img/cepreicono.ico') }}" type="image/x-icon">

    <title>{{ $title ?? config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    {{-- <link href="https://fonts.googleapis.com/css2?family=Handlee&family=Nunito:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Vend+Sans:ital,wght@0,300..700;1,300..700&display=swap"
        rel="stylesheet">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <livewire:navbar />
    {{ $slot }}
    <livewire:footer />
    {{-- --- LÓGICA DE WHATSAPP DINÁMICO --- --}}
    @php
        $whatsapp = \Illuminate\Support\Facades\Cache::remember('institution_whatsapp', now()->addDay(), function () {
            // Si no hay institución, devolvemos un número por defecto o vacío
            return \App\Models\Institution::first()?->whatsapp ?? '51987654321';
        });
    @endphp

    {{-- Llamada al componente --}}
    <x-wsp-mobile :phone="$whatsapp" message="Hola, vengo desde la web principal y necesito información." />
    {{-- ----------------------------------- --}}
    @livewireScripts
    <script src="{{ asset('js/home.js') }}"></script>

</body>

</html>
