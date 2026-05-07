<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Institution;
use App\Models\AcademicCycle;
use App\Models\Convention;
use App\Models\Banner;
use App\Models\AcademicService;
use App\Models\Teacher; // Asumiendo que existe el modelo basado en CicloCourseTeacher

new class extends Component {
    public function with(): array
    {
        return [
            // Obtenemos la primera institución registrada
            'institucion' => Institution::first() ?? new Institution(),

            // Banners ordenados por su columna 'orden'
            'banners' => Banner::where('estado', 'Activo')->orderBy('orden')->get(),

            // Servicios Académicos
            'servicios' => AcademicService::where('estado', 'Activo')->get(),

            // Ciclos con sus detalles cargados (Eager Loading para optimizar)
            // 'ciclos' => AcademicCycle::with('details')->where('estado', true)->orderBy('fecha_inicio', 'asc')->get(),
            'ciclos' => AcademicCycle::with([
                'details',
                'courses' => function ($query) {
                    // Especificamos 'courses.estado' para eliminar la ambigüedad
                    $query->where('courses.estado', 'Activo');
                },
            ])
                ->where('estado', true)
                ->orderBy('fecha_inicio', 'asc')
                ->get(),
            // Convenios Activos
            'convenios' => Convention::where('estado', 'Activo')->get(),

            // Plana docente (Si el modelo Teacher ya existe)
            'docentes' => Teacher::with(['user', 'specialties'])
                ->where('estado', true)
                ->get(),
        ];
    }

    public function render()
    {
        return $this->view()->title('Cepre Vallejo');
    }
};
?>

<div class="academia-root">


    {{-- ============================================================
     1. BANNER — fondo degradado vibrante + olas
============================================================ --}}
    @if ($banners->count() > 0)
        <!-- Agregamos h-full para que ocupe el alto total del padre si este lo tiene definido -->
        {{-- Sección de Banner Principal --}}
        <section wire:ignore class="relative w-full group overflow-hidden bg-white">

            {{-- Swiper Container --}}
            {{-- Ajustamos la altura: h-[320px] en móvil y hasta h-[480px] en desktop --}}
            <div class="swiper mySwiper w-full h-[420px] md:h-[500px] lg:h-[580px] relative z-10"
                style="--swiper-navigation-color:#C9A84C;
               --swiper-pagination-color:#C9A84C;
               --swiper-navigation-size:22px;">

                <div class="swiper-wrapper">
                    @foreach ($banners as $index => $banner)
                        <div class="swiper-slide w-full h-full bg-white">
                            @if ($banner->enlace)
                                <a href="{{ $banner->enlace }}" target="_blank" class="absolute inset-0 z-20"></a>
                            @endif

                            <picture class="w-full h-full flex items-center justify-center">
                                {{-- Imagen para Desktop --}}
                                <source media="(min-width: 768px)"
                                    srcset="{{ Storage::url($banner->imagen_desktop_path) }}">

                                {{-- Imagen para Mobile (o fallback) --}}
                                <img src="{{ Storage::url($banner->imagen_mobile_path ?? $banner->imagen_desktop_path) }}"
                                    alt="Banner {{ $index + 1 }}" {{-- PRIORIDAD ALTURA: object-contain evita que se corte el texto de los lados --}}
                                    class="w-full h-full object-contain md:object-fill pointer-events-none">
                            </picture>
                        </div>
                    @endforeach
                </div>

                {{-- Flechas de Navegación (Solo visibles en hover) --}}
                <div class="swiper-button-next !w-12 !h-12 !rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 !mr-4 shadow-lg after:!text-lg"
                    style="background:rgba(255,255,255,0.9); border:1px solid #e5e7eb;">
                </div>
                <div class="swiper-button-prev !w-12 !h-12 !rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 !ml-4 shadow-lg after:!text-lg"
                    style="background:rgba(255,255,255,0.9); border:1px solid #e5e7eb;">
                </div>

                {{-- Paginación --}}
                <div class="swiper-pagination !bottom-6"></div>
            </div>

            {{-- Decoración: Onda inferior suave para conectar con la siguiente sección --}}
            <div class="absolute bottom-0 left-0 w-full z-20 pointer-events-none">
                <svg viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                    class="w-full h-[40px] md:h-[70px]">
                    <path d="M0,50 C300,100 600,0 900,50 C1200,100 1440,20 1440,50 L1440,100 L0,100 Z" fill="#FFFFFF" />
                </svg>
            </div>
        </section>
    @endif

    {{-- ============================================================
     2. SERVICIOS — blanco limpio con puntos dorados
============================================================ --}}
    @if ($servicios->count() > 0)
        <section id="servicios" class=" relative overflow-hidden" style="background:var(--s1-bg)">

            {{-- Dots grid corner --}}
            <div class="dots-bg absolute top-0 right-0 w-64 h-64 opacity-40 rounded-bl-full pointer-events-none"></div>
            <div class="dots-bg absolute bottom-0 left-0 w-48 h-48 opacity-40 rounded-tr-full pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                <div class="text-center mb-14 reveal">
                    <span class="section-label">Lo que ofrecemos</span>
                    <h2 class="hand text-5xl md:text-5xl font-bold mt-5" style="color:var(--blue)">
                        Servicios <span class="text-gold">Académicos</span>
                    </h2>
                    <span class="gold-rule"></span>
                </div>

                <div class="relative w-full overflow-x-clip reveal">
                    <div wire:ignore class="swiper servicesSwiper !px-4 !py-10 -m-4">
                        <div class="swiper-wrapper">
                            @foreach ($servicios as $servicio)
                                <div class="swiper-slide h-auto flex">
                                    <div class="card flex flex-col w-full group cursor-default">
                                        <div class="w-full h-44 flex items-center justify-center rounded-t-[18px]"
                                            style="background:var(--gold-pale)">
                                            @if ($servicio->imagen_path)
                                                <img src="{{ Storage::url($servicio->imagen_path) }}"
                                                    alt="{{ $servicio->titulo }}"
                                                    class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-500 p-1">
                                            @endif
                                        </div>
                                        <div class="h-[3px] w-0 group-hover:w-full transition-all duration-500 rounded-none"
                                            style="background:linear-gradient(90deg,var(--gold),var(--gold-lt))"></div>
                                        <div
                                            class="px-6 py-5 flex flex-col items-center text-center flex-grow justify-center">
                                            <h3 class="text-lg font-extrabold uppercase tracking-widest mb-2"
                                                style="color:var(--blue)">
                                                {{ $servicio->titulo }}
                                            </h3>
                                            <p class="text-xs md:text-base leading-relaxed line-clamp-4"
                                                style="color:var(--gray)">
                                                {{ $servicio->descripcion }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination !static mt-8"></div>
                    </div>
                </div>
            </div>

            {{-- Onda —> s2 lavanda --}}
            <div class="wave-divider mt-14 pointer-events-none">
                <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                    style="height:90px">
                    <path d="M0,45 C360,90 720,0 1080,55 C1260,78 1380,25 1440,45 L1440,90 L0,90 Z" fill="#F0F4FF" />
                </svg>
            </div>
        </section>
    @endif

    {{-- ============================================================
     3. NOSOTROS — lavanda suave con blobs
============================================================ --}}
    <section id="nosotros" class="pt-10 relative overflow-hidden" style="background:var(--s2-bg)">

        {{-- BG blobs --}}
        <div class="blob float1"
            style="width:420px;height:420px;top:-100px;right:-80px;background:radial-gradient(circle,#C9A84C28,transparent 65%)">
        </div>
        <div class="blob float2"
            style="width:280px;height:280px;bottom:-60px;left:-40px;background:radial-gradient(circle,#6D28D918,transparent 65%)">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                {{-- Image --}}
                <div class="relative reveal-left">
                    {{-- Shadow card behind --}}
                    <div class="absolute -bottom-5 -right-5 w-full h-full rounded-3xl pointer-events-none"
                        style="background:linear-gradient(135deg,var(--gold)33,#6D28D918);border:2px solid var(--gold-border);border-radius:24px">
                    </div>
                    <img src="{{ !empty($institucion->logo_path) ? Storage::url($institucion->logo_path) : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80' }}"
                        alt="Nosotros" class="relative rounded-2xl w-full object-contain"
                        style="height:460px;box-shadow:0 24px 64px rgba(27,61,122,.18)">
                    {{-- Corner accents --}}
                    <div class="absolute -top-3 -left-3 w-12 h-12 rounded-tl-2xl border-t-[3px] border-l-[3px]"
                        style="border-color:var(--gold)"></div>
                    <div class="absolute -bottom-3 -right-3 w-12 h-12 rounded-br-2xl border-b-[3px] border-r-[3px]"
                        style="border-color:var(--gold)"></div>
                </div>

                {{-- Text --}}
                <div class="reveal-right">
                    <span class="section-label">Conócenos</span>
                    <h2 class="hand text-4xl md:text-5xl font-bold mt-4 mb-1" style="color:var(--blue)">
                        ¿Por qué elegirnos?
                    </h2>
                    <p class="hand text-2xl font-bold mb-1 text-gold">
                        {{ $institucion->razon_social ?? 'Nuestra Academia' }}
                    </p>
                    <span class="gold-rule left" style="margin-bottom:0"></span>
                    <div class="render-html prose-lg md:prose-xl max-w-none mt-7 leading-relaxed prose-slate prose-p:text-slate-900 prose-li:text-slate-900 prose-strong:text-slate-900"
                        style="color: var(--gray);">
                        {!! $institucion->nosotros !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Onda —> s3 durazno --}}
        <div class="wave-divider mt-16 pointer-events-none">
            <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                style="height:90px">
                <path d="M0,10 C300,80 600,0 900,60 C1100,100 1320,20 1440,50 L1440,90 L0,90 Z" fill="#FFFFFF" />
            </svg>
        </div>
    </section>

    {{-- ============================================================
     4. CICLOS — durazno cálido
============================================================ --}}
    <section id="ciclos" class="relative overflow-hidden" style="background:var(--s1-bg)">

        <div class="blob float2"
            style="width:500px;height:500px;top:-150px;left:50%;transform:translateX(-50%);background:radial-gradient(circle,#C9A84C1A,transparent 65%)">
        </div>
        <div class="dots-bg absolute top-0 left-0 w-56 h-56 opacity-30 rounded-br-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

            <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                <span class="section-label">Matrículas abiertas</span>
                <h2 class="hand text-5xl md:text-5xl font-bold mt-4 mb-2" style="color:var(--blue)">
                    Ciclos <span class="text-gold">Académicos</span>
                </h2>
                <span class="gold-rule"></span>
                <p class="mt-4 text-sm" style="color:var(--gray)">Inscríbete hoy y asegura tu vacante.</p>
            </div>

            <div class="space-y-5 stagger">
                @forelse($ciclos as $ciclo)
                    <div class="ciclo-card group relative">

                        {{-- Left gold bar --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            style="background:linear-gradient(180deg,var(--gold),var(--gold-lt))"></div>

                        <div class="relative flex flex-col lg:flex-row items-stretch">

                            {{-- Name --}}
                            <div class="p-7 lg:w-[28%] flex flex-col justify-center gap-3 border-b lg:border-b-0 lg:border-r"
                                style="border-color:#F0E8CC">
                                <h3 class="hand text-2xl font-bold" style="color:var(--blue)">{{ $ciclo->nombre }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs font-extrabold" style="color:var(--gold)">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Inicio: {{ \Carbon\Carbon::parse($ciclo->fecha_inicio)->format('d/m/Y') }}
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="p-7 lg:w-[44%] flex items-center border-b lg:border-b-0 lg:border-r"
                                style="border-color:#F0E8CC">
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
                                    @foreach ($ciclo->details as $detalle)
                                        <li class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center"
                                                style="background:var(--gold-pale)">
                                                @php
                                                    try {
                                                        $icono = svg($detalle->icono, 'w-4 h-4')->toHtml();
                                                    } catch (\Exception $e) {
                                                        $icono =
                                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                                                    }
                                                @endphp
                                                <span style="color:var(--gold)">{!! $icono !!}</span>
                                            </div>
                                            <span class="text-sm md:text-base font-medium" style="color:var(--gray)">
                                                {{ $detalle->nombre }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Price --}}
                            <div class="p-7 lg:w-[28%] flex flex-col items-center justify-center gap-5"
                                style="background:var(--gold-pale)">
                                <div class="text-center">
                                    <span class="text-xs font-black tracking-widest uppercase block mb-1"
                                        style="color:var(--gold)">
                                        Inversión
                                    </span>
                                    <div class="flex items-start justify-center gap-1">
                                        <span class="font-extrabold text-lg mt-1" style="color:var(--gold)">S/</span>
                                        <span class="hand text-5xl font-bold"
                                            style="color:var(--blue)">{{ number_format($ciclo->precio, 0) }}</span>
                                        <span class="text-sm mt-3"
                                            style="color:var(--gray)">.{{ substr(number_format($ciclo->precio, 2), -2) }}</span>
                                    </div>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $institucion->whatsapp ?? '') }}?text=Hola,+quiero+matricularme+en+{{ urlencode($ciclo->nombre) }}"
                                    target="_blank"
                                    class="w-full text-center py-3 px-6 rounded-xl text-sm font-extrabold text-white transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                                    style="background:linear-gradient(135deg,var(--blue),#2456A4);box-shadow:0 4px 18px rgba(27,61,122,.25)">
                                    Inscríbete Ahora →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center" style="color:var(--gray)">Pronto abriremos nuevas matrículas.</p>
                @endforelse
            </div>
        </div>

        {{-- Onda —> s4 menta --}}
        <div class="wave-divider mt-16 pointer-events-none">
            <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                style="height:90px">
                <path d="M0,55 C240,0 480,90 720,40 C960,-10 1200,80 1440,30 L1440,90 L0,90 Z" fill="#F0F4FF" />
            </svg>
        </div>
    </section>


    @foreach ($ciclos as $ciclo)
        @if ($ciclo->courses->count() > 0)
            <section id="cursos" class="relative overflow-hidden bg-gray-50/50 hand"
                style="background:var(--s2-bg)">

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                    <div class="text-center mb-14">
                        <span class="text-blue-600 font-bold tracking-widest uppercase text-xs">Plan de estudios</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold mt-5 text-blue-900">
                            Cursos del <span class="hand text-5xl md:text-5xl font-bold mt-4 text-gold"
                                style="color:var(--gold)">{{ $ciclo->nombre }}</span>
                        </h2>
                        <div class="w-16 h-1 bg-yellow-500 mx-auto mt-6 rounded-full"></div>
                    </div>

                    <div class="relative w-full overflow-x-clip">
                        {{-- Importante: Usamos !py-12 para que las sombras del hover no se corten --}}
                        <div wire:ignore class="swiper coursesSwiper !px-4 !py-4 -m-4">
                            <div class="swiper-wrapper">
                                @foreach ($ciclo->courses as $curso)
                                    <div class="swiper-slide h-auto flex">
                                        <div
                                            class="bg-white rounded-[2.5rem] p-2 flex flex-col w-full group cursor-pointer transition-all duration-500 hover:shadow-2xl border border-transparent hover:border-gray-100">

                                            <!-- Imagen -->
                                            <div
                                                class="relative w-full h-48 bg-gray-50 rounded-[2rem] overflow-hidden flex items-center justify-center group-hover:bg-blue-50/30 transition-colors">
                                                @if ($curso->imagen_path)
                                                    <img src="{{ Storage::url($curso->imagen_path) }}"
                                                        alt="{{ $curso->nombre }}"
                                                        class="max-w-full max-h-full object-contain transition-transform duration-700 group-hover:scale-105">
                                                @endif
                                            </div>

                                            <!-- Contenido -->
                                            <div class="px-6 py-2 flex flex-col items-center text-center">
                                                <h3
                                                    class="text-2xl font-bold text-slate-900 mb-2 group-hover:text-blue-700 transition-colors">
                                                    {{ $curso->nombre }}
                                                </h3>

                                                <p
                                                    class="text-gray-700 text-sm md:text-base leading-relaxed line-clamp-3 mb-6 font-medium">
                                                    {{ $curso->descripcion ?? 'Contenido especializado para tu preparación.' }}
                                                </p>

                                                {{-- <a href="{{ route('cursos.show', $curso->slug) }}"  --}}
                                                <a href="{{ route('cursos.show', $curso->slug) }}"
                                                    class="inline-flex items-center gap-1 py-3 px-6 rounded-xl bg-gray-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest transition-all group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-lg">
                                                    <span>Ver temario</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination !static mt-8"></div>
                        </div>
                    </div>
                </div>

                {{-- Onda —> s4 menta --}}
                <div class="wave-divider mt-5 pointer-events-none">
                    <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                        style="height:90px">
                        <path d="M0,55 C240,0 480,90 720,40 C960,-10 1200,80 1440,30 L1440,90 L0,90 Z"
                            fill="#FFFFFF" />
                    </svg>
                </div>
            </section>
        @endif
    @endforeach

    {{-- ============================================================
     5. PLANA DOCENTE — verde menta suave
============================================================ --}}
    @if (count($docentes) > 0)
        <section id="docentes" class="relative overflow-hidden" style="background:var(--s1-bg)">

            <div class="blob float3"
                style="width:350px;height:350px;top:-50px;right:-50px;background:radial-gradient(circle,#0F766E22,transparent 65%)">
            </div>
            <div class="dots-bg absolute bottom-0 right-0 w-64 h-64 opacity-25 rounded-tl-full pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                <div class="text-center mb-14 reveal">
                    <span class="section-label">Excelencia educativa</span>
                    <h2 class="hand text-5xl md:text-5xl font-bold mt-4" style="color:var(--blue)">
                        Nuestra <span class="text-gold">Plana Docente</span>
                    </h2>
                    <span class="gold-rule"></span>
                </div>

                <div class="relative w-full overflow-x-clip reveal">
                    <div wire:ignore class="swiper teachersSwiper !px-4 !py-10 -m-4">
                        <div class="swiper-wrapper">
                            @foreach ($docentes as $docente)
                                <div class="swiper-slide h-auto flex">
                                    <div class="card flex flex-col items-center text-center w-full p-8 group">

                                        {{-- Avatar --}}
                                        <div class="relative w-32 h-32 mb-5">
                                            <div class="absolute -inset-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                                style="background:conic-gradient(var(--gold),var(--blue),var(--gold));padding:2px;border-radius:9999px">
                                            </div>
                                            <img src="{{ $docente->imagen_path ? Storage::url($docente->imagen_path) : asset('img/default-avatar.png') }}"
                                                alt="{{ $docente->user->name }}"
                                                class="relative w-full h-full object-cover rounded-full border-4 border-white shadow-md">
                                        </div>

                                        <h3 class="text-base font-extrabold mb-1 group-hover:text-[var(--blue)] transition-colors"
                                            style="color:var(--text)">
                                            {{ $docente->user->name }}
                                        </h3>
                                        <p class="text-sm leading-relaxed line-clamp-3 italic mb-5"
                                            style="color:var(--gray)">
                                            "{{ $docente->biografia ?? 'Docente especializado en la formación preuniversitaria.' }}"
                                        </p>

                                        <div class="mt-auto pt-5 border-t w-full" style="border-color:#D1E8E0">
                                            <span
                                                class="text-[9px] font-extrabold tracking-widest uppercase block mb-2"
                                                style="color:var(--gold)">Especialista en:</span>
                                            <div class="flex flex-wrap justify-center gap-1.5">
                                                @foreach ($docente->specialties->take(3) as $esp)
                                                    <span class="text-[10px] font-bold px-3 py-1 rounded-full"
                                                        style="color:var(--blue);background:#E8F0FE;border:1px solid #C7D8FA">
                                                        {{ $esp->nombre }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination !static mt-10"></div>
                    </div>
                </div>
            </div>

            {{-- Onda —> s5 lila --}}
            <div class="wave-divider mt-12 pointer-events-none">
                <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                    style="height:90px">
                    <path d="M0,20 C360,90 720,0 1080,65 C1260,95 1380,30 1440,50 L1440,90 L0,90 Z" fill="#FFFFFF" />
                </svg>
            </div>
        </section>
    @endif

    {{-- ============================================================
     6. CONVENIOS — lila pálido
============================================================ --}}
    @if ($convenios->count() > 0)
        <section x-data="{ openModal: false, activeConvention: {} }" id="convenios" class="relative overflow-hidden"
            style="background:var(--s1-bg)">

            <div class="blob float1"
                style="width:400px;height:400px;top:-100px;left:-80px;background:radial-gradient(circle,#6D28D91A,transparent 65%)">
            </div>
            <div class="dots-bg absolute bottom-0 left-0 w-72 h-72 opacity-25 rounded-tr-full pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                <div class="text-center mb-14 reveal">
                    <span class="section-label">Alianzas estratégicas</span>
                    <h2 class="hand text-5xl md:text-5xl font-bold mt-4" style="color:var(--blue)">
                        Convenios <span class="text-gold">Institucionales</span>
                    </h2>
                    <span class="gold-rule"></span>
                </div>

                <div class="relative w-full overflow-x-clip reveal">
                    <div wire:ignore class="swiper conventionsSwiper !px-4 !py-10 -m-4">
                        <div class="swiper-wrapper">
                            @foreach ($convenios as $convenio)
                                <div class="swiper-slide h-auto flex">
                                    <div @click="activeConvention={{ json_encode($convenio) }}; openModal=true"
                                        class="card flex flex-col w-full cursor-pointer group overflow-hidden">

                                        @php
                                            $est = strtolower($convenio->estado_convenio ?? 'activo');
                                            $badge = match ($est) {
                                                'finalizado'
                                                    => 'background:#FEE2E2;color:#DC2626;border:1px solid #FECACA',
                                                'pendiente'
                                                    => 'background:#FEF9C3;color:#A16207;border:1px solid #FDE68A',
                                                default => 'background:#DCFCE7;color:#15803D;border:1px solid #BBF7D0',
                                            };
                                        @endphp

                                        <div class="relative h-44 flex items-center justify-center rounded-t-[18px]"
                                            style="background:var(--gold-pale)">
                                            <span
                                                class="absolute top-3 right-3 text-[9px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full z-100"
                                                style="{{ $badge }}">
                                                {{ $convenio->estado_convenio ?? 'Activo' }}
                                            </span>
                                            <img src="{{ Storage::url($convenio->imagen_path) }}"
                                                alt="{{ $convenio->nombre }}"
                                                class="max-w-full max-h-full object-contain grayscale group-hover:grayscale-0
                                            opacity-70 group-hover:opacity-100 transition-all duration-500 group-hover:scale-105">
                                        </div>
                                        <div class="h-[3px] w-0 group-hover:w-full transition-all duration-500"
                                            style="background:linear-gradient(90deg,var(--gold),var(--gold-lt))"></div>
                                        <div class="px-6 py-5 flex flex-col items-center text-center">
                                            <h3 class="text-sm font-extrabold mb-1 group-hover:text-[var(--gold)] transition-colors"
                                                style="color:var(--text)">
                                                {{ $convenio->nombre }}
                                            </h3>
                                            <p class="text-[9px] uppercase tracking-widest font-extrabold mb-4"
                                                style="color:var(--gray)">Alianza Estratégica</p>
                                            <div class="flex items-center gap-2 text-xs font-extrabold group-hover:gap-3 transition-all duration-300"
                                                style="color:var(--gold)">
                                                <span>Ver detalles</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination !static mt-10"></div>
                    </div>
                </div>
            </div>

            {{-- MODAL --}}
            <div x-show="openModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                style="display:none">
                <div @click="openModal=false" class="fixed inset-0 backdrop-blur-md"
                    style="background:rgba(235, 235, 235, 0.12)"></div>
                <div x-show="openModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative w-full max-w-4xl z-10 rounded-3xl overflow-hidden bg-white"
                    style="border:1.5px solid var(--gold-border);box-shadow:0 30px 80px rgba(201,168,76,.2),0 8px 30px rgba(0,0,0,.08)">

                    <button @click="openModal=false"
                        class="absolute top-5 right-5 z-30 w-9 h-9 rounded-full flex items-center justify-center transition-colors hover:bg-[var(--gold)] hover:text-white"
                        style="background:#F3F4F6;color:#64748B;border:1px solid #E5E7EB">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="flex flex-col md:flex-row min-h-[400px]">
                        <div class="md:w-5/12 h-56 md:h-auto relative overflow-hidden"
                            style="background:var(--gold-pale);border-right:1.5px solid var(--gold-border)">
                            <img :src="'/storage/' + activeConvention.imagen_path"
                                class="absolute inset-0 w-full h-full object-contain p-8 transition-transform duration-700 hover:scale-105">
                        </div>
                        <div class="md:w-7/12 p-10 md:p-14 flex flex-col justify-center">
                            <span class="section-label mb-3" x-text="'Convenio ' + activeConvention.periodo"></span>
                            <h2 class="hand text-3xl font-bold mb-1" style="color:var(--blue)"
                                x-text="activeConvention.nombre"></h2>
                            <span class="gold-rule left" style="margin-bottom:1.5rem"></span>
                            <p class="text-sm leading-relaxed mb-8" style="color:var(--gray)"
                                x-text="activeConvention.descripcion"></p>
                            <div class="grid grid-cols-2 gap-6 pt-7 border-t" style="border-color:var(--gold-border)">
                                <div>
                                    <span class="text-[9px] font-extrabold tracking-widest uppercase block mb-2"
                                        style="color:var(--gold)">Representante</span>
                                    <p class="text-sm font-bold" style="color:var(--text)"
                                        x-text="activeConvention.representante ?? 'Institución'"></p>
                                </div>
                                <div>
                                    <span class="text-[9px] font-extrabold tracking-widest uppercase block mb-2"
                                        style="color:var(--gold)">Estado Actual</span>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full animate-pulse"
                                            style="background:#22C55E"></span>
                                        <p class="text-sm font-bold" style="color:#15803D"
                                            x-text="activeConvention.estado_convenio"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
