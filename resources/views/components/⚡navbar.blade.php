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
                            class="flex items-center gap-2 border-2 border-[var(--gold)] text-blue-900 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[var(--gold-pale)] transition-all duration-300">
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
                            <a href="{{ config('app.url') }}/admin"
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
                            <a href="{{ config('app.url') }}/admin"
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
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Panel de
                                        Profesores</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- BOTÓN MATRÍCULA -->
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $institucion->whatsapp ?? '') }}"
                        target="_blank"
                        class="bg-blue-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all duration-300">
                        Matrícula Online
                    </a>
                </div>
            </div>

            <!-- Botón Hamburguesa Móvil -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="p-2 rounded-xl text-blue-900 hover:bg-blue-50 transition-all">
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
        <div class="px-6 pt-4 pb-10 space-y-1">
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

            <!-- Botón WhatsApp -->
            <div class="pt-2">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $institucion->whatsapp ?? '') }}"
                    target="_blank"
                    class="flex items-center justify-center gap-3 w-full bg-[#25D366] text-white px-4 py-5 rounded-[1.5rem] text-sm font-black uppercase tracking-[0.1em] shadow-xl shadow-green-100 active:scale-95 transition-all">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Matrícula Online
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
