<?php

use Livewire\Component;
use App\Models\Institution;

new class extends Component {
    public $institucion;

    public function mount()
    {
        $this->institucion = Institution::first() ?? new Institution();
    }
};
?>

<nav x-data="{
    open: false,
    atTop: true,
    intranetOpen: false,
    activeSection: ''
}" x-init="const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            activeSection = entry.target.id;
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('section[id]').forEach((section) => {
    observer.observe(section);
});" @scroll.window="atTop = (window.pageYOffset > 40 ? false : true)"
    @click.away="intranetOpen = false"
    :class="{ 'bg-white/95 backdrop-blur-md shadow-lg py-2': !atTop, 'bg-white py-4': atTop }"
    class="fixed top-0 w-full z-[100] transition-all duration-500 border-b border-gray-100 hand">

    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo e Institución -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div
                    class="flex-shrink-0 flex items-center h-12 w-12 rounded-2xl border border-blue-50 bg-white transition-all duration-500 group-hover:shadow-lg group-hover:rotate-3">
                    @if ($institucion->logo_path)
                        <img class="max-h-full max-w-full object-contain rounded-2xl p-1.5"
                            src="{{ Storage::url($institucion->logo_path) }}" alt="Logo">
                    @else
                        <div
                            class="w-full h-full bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-xs">
                            CV</div>
                    @endif
                </div>

                <div class="hidden lg:block">
                    <span
                        class="text-blue-900 font-black text-xl tracking-tighter uppercase group-hover:text-blue-600 transition-colors">
                        {{ $institucion->razon_social ?? 'Academia' }}
                    </span>
                </div>
            </a>

            <!-- Menú Desktop -->
            <div class="hidden md:flex items-center space-x-6">
                <!-- En el Menú Desktop, actualiza las clases condicionales -->
                <nav class="flex items-center space-x-6 mr-4">
                    <a href="{{ url('/') }}#servicios"
                        :class="activeSection === 'servicios' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Servicios</a>
                    <a href="{{ url('/') }}#nosotros"
                        :class="activeSection === 'nosotros' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Nosotros</a>
                    <a href="{{ url('/') }}#ciclos"
                        :class="activeSection === 'ciclos' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Ciclos</a>
                    <a href="{{ url('/') }}#cursos"
                        :class="activeSection === 'cursos' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Cursos</a>
                    <a href="{{ url('/') }}#docentes"
                        :class="activeSection === 'docentes' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Docentes</a>
                    <a href="{{ url('/') }}#convenios"
                        :class="activeSection === 'convenios' ? 'text-blue-700 scale-105' : 'text-slate-900'"
                        class="nav-link">Convenios</a>
                </nav>

                <div class="flex items-center gap-3">
                    <!-- BOTÓN INICIAR SESIÓN -->
                    <div class="relative">
                        <button @click="intranetOpen = !intranetOpen"
                            class="cursor-pointer flex items-center gap-2 border-2 border-[var(--gold)] text-blue-900 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[var(--gold-pale)] transition-all duration-300">
                            <svg class="w-4 h-4 text-[var(--gold)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Iniciar Sesión
                        </button>

                        <!-- Dropdown Intranet -->
                        <div x-show="intranetOpen" x-transition ...
                            class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden z-[110]"
                            x-cloak>
                            <div class="px-5 py-1 border-b border-gray-50 bg-gray-50/50">
                                <span class="text-[12px] font-black text-gray-400 uppercase tracking-[0.15em]">Portal
                                    Intranet</span>
                            </div>
                            <a href="{{ config('app.url') }}/alumno"
                                class="group flex items-center gap-4 px-5 py-2 hover:bg-blue-50 transition-all duration-300">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col text-left">
                                    <span
                                        class="text-base font-black text-blue-900 group-hover:text-blue-600 transition-colors">Soy
                                        Alumno</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Acceso
                                        Estudiantes</span>
                                </div>
                            </a>
                            <a href="{{ config('app.url') }}/profesor"
                                class="group flex items-center gap-4 px-5 py-2 hover:bg-amber-50 transition-all duration-300">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col text-left">
                                    <span
                                        class="text-base font-black text-blue-900 group-hover:text-amber-600 transition-colors">Soy
                                        Docente</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Panel
                                        de
                                        Profesores</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- BOTÓN MATRÍCULA -->
                    <div class="relative group inline-flex items-center">
                        <!-- Tooltip corregido (ahora aparece abajo para evitar que se corte arriba) -->
                        <div
                            class="absolute top-full mt-3 left-1/2 -translate-x-1/2 px-3 py-2 bg-gray-900 text-white text-[11px] font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none whitespace-nowrap shadow-2xl z-50">
                            {{ $institucion->whatsapp ?? '999 999 999' }}
                            <!-- Flechita del tooltip apuntando hacia arriba -->
                            <div
                                class="absolute bottom-full left-1/2 -translate-x-1/2 border-8 border-transparent border-b-gray-900">
                            </div>
                        </div>

                        <!-- Botón de WhatsApp -->
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $institucion->whatsapp ?? '') }}"
                            target="_blank"
                            class="flex items-center gap-2 bg-[#25D366] text-white px-5 py-3 rounded-2xl text-[12px] font-black uppercase tracking-wider shadow-lg shadow-green-100 hover:bg-[#20ba5a] hover:-translate-y-0.5 active:scale-95 transition-all duration-300">

                            <!-- Nuevo Icono de WhatsApp más limpio -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-whatsapp" viewBox="0 0 16 16">
                                <path
                                    d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                            </svg>

                            MATRICÚLATE AHORA
                        </a>
                    </div>
                </div>
            </div>

            <!-- Botón Hamburguesa Móvil -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="p-2 rounded-xl text-blue-900 hover:bg-blue-50 transition-all cursor-pointer">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú Móvil -->
    <div x-show="open" x-cloak x-transition ...
        class="md:hidden bg-white border-t border-gray-50 shadow-[0_20px_50px_rgba(0,0,0,0.1)] absolute w-full z-[100] rounded-b-[2.5rem]">
        <div class="p-5 space-y-1">
            <div class="py-2">
                <a href="{{ url('/') }}#servicios" @click="open = false"
                    :class="activeSection === 'servicios' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Servicios</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/') }}#nosotros" @click="open = false"
                    :class="activeSection === 'nosotros' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Nosotros</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/') }}#ciclos" @click="open = false"
                    :class="activeSection === 'ciclos' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Ciclos</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/') }}#cursos" @click="open = false"
                    :class="activeSection === 'cursos' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Cursos</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/') }}#docentes" @click="open = false"
                    :class="activeSection === 'docentes' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Docentes</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/') }}#convenios" @click="open = false"
                    :class="activeSection === 'convenios' ? 'text-blue-600' : 'text-gray-700'"
                    class="group flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm font-black uppercase tracking-widest transition-colors">Convenios</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Sección Intranet Móvil -->
            <div class="grid grid-cols-2 gap-4 py-4">
                <a href="{{ config('app.url') }}/admin"
                    class="flex flex-col items-center justify-center p-4 rounded-[2rem] bg-blue-50 border border-blue-100 active:scale-95 transition-all">
                    <div
                        class="w-10 h-10 mb-3 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Intranet</span>
                    <span class="text-xs font-bold text-blue-900 mt-0.5">Alumnos</span>
                </a>
                <a href="{{ config('app.url') }}/profesor"
                    class="flex flex-col items-center justify-center p-4 rounded-[2rem] bg-amber-50 border border-amber-100 active:scale-95 transition-all">
                    <div class="w-10 h-10 mb-3 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-200"
                        style="background-color: var(--gold);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest"
                        style="color: var(--gold);">Intranet</span>
                    <span class="text-xs font-bold text-amber-900 mt-0.5">Docentes</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    [x-cloak] {
        display: none !important;
    }

    .nav-link {
        @apply hover:text-blue-600 text-[12px] font-black uppercase tracking-widest transition-all;
    }

    .dropdown-item {
        @apply flex items-center justify-between px-4 py-3 text-[11px] font-black uppercase tracking-widest text-gray-600 hover:bg-blue-600 hover:text-white transition-all;
    }
</style>
