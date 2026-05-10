<?php

use Livewire\Component;
use App\Models\Course;
use App\Models\Institution;
use App\Models\CicloCourse;
use App\Models\CicloCourseTeacher;
use Livewire\Attributes\Layout;

new class extends Component {
    public $course;
    public $institucion;
    public $docentes;

    public function mount($slug)
    {
        // 1. Cargamos el curso con contenidos y detalles
        $this->course = Course::with([
            'contents' => function ($query) {
                $query->where('estado', 'activo')->orderBy('orden', 'asc');
            },
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->institucion = Institution::first() ?? new Institution();

        // 2. Obtenemos los docentes de este curso a través de la tabla pivote
        // Buscamos los IDs de la relación ciclo_curso para este curso
        $cicloCourseIds = CicloCourse::where('course_id', $this->course->id)->pluck('id');

        // Buscamos los docentes asignados a esos registros
        $this->docentes = CicloCourseTeacher::whereIn('ciclo_course_id', $cicloCourseIds)
            ->with(['teacher.user'])
            ->get()
            ->map(fn($ct) => $ct->teacher)
            ->unique('id');
    }

    public function render()
    {
        return $this->view()->layout('layouts::app', [
            'title' => $this->course->nombre . ' | ' . $this->institucion->razon_social,
        ]);
    }
    // public function render()
    // {
    //     return view('livewire.view-course');
    // }
};
?>
@php
    $docentesArr = $docentes ?? collect();
@endphp

<div x-data="{
    active: null,
    imgModal: false,
    imgUrl: '',
    openModal(url) {
        if (!url) return;
        this.imgUrl = url;
        this.imgModal = true;
    }
}" class="bg-gray-50 h-auto pb-20">

    <!-- Header Hero -->
    <section class="relative bg-blue-900 pt-16 pb-32 overflow-hidden hand">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-800/20 skew-x-12 translate-x-20"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white text-left">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center gap-2 text-blue-300 hover:text-white mb-6 text-xs font-bold uppercase tracking-widest transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al inicio
                    </a>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">{{ $course->nombre }}</h1>
                    <div class="flex flex-wrap gap-4 mb-8">
                        <span class="px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--gold); color: white;">
                            Código: {{ $course->codigo }}
                        </span>
                        <span
                            class="bg-blue-800 text-blue-100 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ $course->horas_semanales }} Horas Semanales
                        </span>
                    </div>
                    <p class="text-blue-100 text-lg leading-relaxed max-w-xl font-medium">
                        {{ $course->descripcion ?? 'Potencia tus conocimientos con el mejor método de enseñanza.' }}
                    </p>
                </div>

                <div class="relative flex justify-center">
                    <div class="w-full max-w-md bg-white rounded-[2.5rem] p-4 shadow-2xl transform lg:rotate-3 cursor-zoom-in"
                        @click="openModal('{{ $course->imagen_path ? Storage::url($course->imagen_path) : '' }}')">
                        <div class="aspect-video rounded-[2rem] overflow-hidden flex items-center justify-center border-4 border-white"
                            style="background-color: var(--gold-pale);">
                            @if ($course->imagen_path)
                                <img src="{{ Storage::url($course->imagen_path) }}" alt="{{ $course->nombre }}"
                                    class="w-full h-full object-contain p-4">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 hand">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Columna Izquierda: Temario y Docentes -->
            <div class="lg:col-span-2 space-y-12">

                <!-- Temario -->
                <div class="bg-white rounded-2xl md:rounded-[2.5rem] p-6 md:p-10 shadow-xl border border-gray-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center"
                            style="background-color: var(--gold-pale);">
                            <svg class="w-6 h-6" style="color: var(--gold);" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900 tracking-tight text-left">Temario
                            del Curso</h2>
                    </div>

                    <div class="space-y-3 text-left">
                        @forelse($course->contents as $index => $content)
                            <div class="border border-gray-100 rounded-xl md:rounded-2xl overflow-hidden transition-all duration-300"
                                :class="active === {{ $index }} ? 'shadow-md border-blue-100 ring-1 ring-blue-50' : ''">
                                <button @click="active = (active === {{ $index }} ? null : {{ $index }})"
                                    class="w-full px-4 py-4 md:px-6 md:py-5 flex items-center justify-between bg-white hover:bg-gray-50 transition-colors text-left gap-3">
                                    <div class="flex items-center gap-3 md:gap-5 min-w-0">
                                        <span class="text-xs md:text-sm font-bold tabular-nums flex-shrink-0"
                                            style="color: var(--gold);">{{ sprintf('%02d', $index + 1) }}</span>
                                        <span
                                            class="text-sm md:text-base font-bold text-gray-700 leading-tight truncate md:whitespace-normal">{{ $content->titulo }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-blue-300 flex-shrink-0 transition-transform duration-300"
                                        :class="active === {{ $index }} ? 'rotate-180 text-blue-500' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="active === {{ $index }}" x-collapse
                                    class="px-4 pb-5 md:px-6 md:pb-6 text-gray-500 text-sm leading-relaxed">
                                    <div class="pt-2 pl-7 md:pl-10 border-l-2" style="border-color: var(--gold-pale);">
                                        {{ $content->descripcion ?? 'Contenido detallado en preparación para tu ingreso.' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <p class="text-gray-400 text-sm font-medium italic">Temario no disponible.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- SECCIÓN DOCENTES -->
                @if ($docentesArr->count() > 0)
                    <div class="bg-white rounded-2xl md:rounded-[2.5rem] p-6 md:p-10 shadow-xl border border-gray-100">
                        <div class="flex items-center gap-3 mb-8 text-left">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center"
                                style="background-color: var(--gold-pale);">
                                <svg class="w-6 h-6" style="color: var(--gold);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900 tracking-tight">Plana Docente
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                            @foreach ($docentesArr as $teacher)
                                <div class="group flex items-center gap-4 p-3 md:p-4 rounded-xl md:rounded-2xl border border-gray-50 bg-gray-50/50 hover:bg-white hover:border-[var(--gold-border)] hover:shadow-md transition-all duration-300 cursor-pointer"
                                    @click="openModal('{{ $teacher->imagen_path ? Storage::url($teacher->imagen_path) : '' }}')">
                                    <div
                                        class="w-16 h-16 md:w-20 md:h-20 rounded-xl md:rounded-2xl overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover:border-[var(--gold)] transition-all duration-300">
                                        @if ($teacher->imagen_path)
                                            <img src="{{ Storage::url($teacher->imagen_path) }}"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-left min-w-0">
                                        <h4 class="font-bold text-blue-900 leading-tight text-sm md:text-base truncate">
                                            {{ $teacher->user->name }}</h4>
                                        <p class="text-[10px] md:text-xs font-bold uppercase tracking-widest mt-1"
                                            style="color: var(--gold);">Especialista</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Columna lateral: CTA -->
            <!-- Columna lateral: CTA -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-8">
                    <!-- Tarjeta con Borde Dorado Permanente y Efecto de Resplandor -->
                    <div
                        class="group relative bg-blue-900 rounded-[3rem] p-10 text-white overflow-hidden text-center transition-all duration-500 
                    border-2 border-[var(--gold)] 
                    shadow-[0_0_20px_rgba(201,168,76,0.2)] 
                    hover:shadow-[0_0_50px_rgba(201,168,76,0.4)] 
                    hover:border-[var(--gold-lt)]">

                        <!-- Círculos decorativos con movimiento (Glow interno) -->
                        <div
                            class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500 rounded-full opacity-20 blur-3xl transition-transform duration-1000 group-hover:-translate-x-10 group-hover:translate-y-10">
                        </div>
                        <div
                            class="absolute -bottom-10 -left-10 w-32 h-32 bg-[var(--gold)] rounded-full opacity-10 blur-2xl transition-transform duration-1000 group-hover:translate-x-10 group-hover:-translate-y-10">
                        </div>

                        <!-- Icono Superior Glassmorphism con borde dorado -->
                        <div
                            class="relative z-10 mb-8 inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md border border-[var(--gold-border)] transition-all duration-500 group-hover:rotate-12 group-hover:scale-110 shadow-xl">
                            <svg class="w-10 h-10 text-[var(--gold-lt)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>

                        <div class="relative z-10">
                            <h3 class="text-3xl font-black mb-4 tracking-tight leading-none">¡Inicia hoy mismo!</h3>
                            <p class="text-blue-100 text-lg mb-10 leading-relaxed opacity-80 font-medium">
                                Asegura tu vacante y prepárate con los mejores especialistas.
                            </p>

                            <!-- Botón de WhatsApp con Brillo Animado -->
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $institucion->whatsapp ?? '') }}?text=Hola, información del curso: {{ urlencode($course->nombre) }}"
                                target="_blank"
                                class="relative flex items-center justify-center w-full bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold py-5 px-6 rounded-2xl transition-all duration-300 shadow-[0_15px_30px_rgba(37,211,102,0.3)] hover:shadow-[0_20px_40px_rgba(37,211,102,0.4)] hover:-translate-y-1.5 overflow-hidden group/btn">

                                <span
                                    class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover/btn:animate-shine"></span>

                                <div class="flex items-center gap-3 relative z-10">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                    <span class="text-lg font-black relative z-10">Preguntar por WhatsApp</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIGHTBOX MODAL -->
    <template x-teleport="body">
        <div x-show="imgModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-blue-950/90 backdrop-blur-md"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
            <button @click="imgModal = false"
                class="absolute top-6 right-6 text-white hover:text-[var(--gold-lt)] transition-colors z-[110]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18">
                    </path>
                </svg>
            </button>
            <div class="relative max-w-5xl w-full flex justify-center" @click.away="imgModal = false">
                <img :src="imgUrl"
                    class="rounded-3xl shadow-2xl max-h-[85vh] max-w-full object-contain border-4 border-white/20">
            </div>
        </div>
    </template>

    <style>

    </style>
</div>
