<?php

use Livewire\Component;
use App\Models\Institution;

new class extends Component {
    public $institucion;

    public function mount()
    {
        // Recuperamos la información de la institución para que el footer sea dinámico
        $this->institucion = Institution::first() ?? new Institution();
    }
};
?>

<footer class="bg-blue-900 text-white pt-20 pb-10 border-t border-gray-800 hand">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Rejilla Principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

            <!-- Columna 1: Marca y Redes Sociales -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-white rounded-2xl p-2 flex items-center justify-center">
                        @if ($institucion->logo_path)
                            <img class="max-h-full max-w-full object-contain"
                                src="{{ Storage::url($institucion->logo_path) }}"
                                alt="Logo {{ $institucion->razon_social }}">
                        @endif
                    </div>
                    <span class="text-xl font-extrabold tracking-tight uppercase">
                        {{ $institucion->razon_social ?? 'Academia' }}
                    </span>
                </div>

                <p class="text-white text-sm leading-relaxed font-medium">
                    RUC: {{ $institucion->ruc ?? 'N/A' }}<br>
                    Líderes en preparación preuniversitaria con excelencia académica.
                </p>

                <!-- Redes Sociales Dinámicas -->
                <div class="flex gap-3">
                    @if ($institucion->facebook_url)
                        <a href="{{ $institucion->facebook_url }}" target="_blank"
                            class="w-9 h-9 rounded-xl bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif
                    @if ($institucion->instagram_url)
                        <a href="{{ $institucion->instagram_url }}" target="_blank"
                            class="w-9 h-9 rounded-xl bg-gray-800 flex items-center justify-center hover:bg-pink-600 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    @endif
                    @if ($institucion->tiktok_url)
                        <a href="{{ $institucion->tiktok_url }}" target="_blank"
                            class="w-9 h-9 rounded-xl bg-gray-800 flex items-center justify-center hover:bg-black transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.525.02c1.31.036 2.512.335 3.6.895V7.03c-.765-.545-1.63-.885-2.575-1.025v4.545c0 3.12-2.53 5.65-5.65 5.65s-5.65-2.53-5.65-5.65c0-3.12 2.53-5.65 5.65-5.65.29 0 .57.025.845.065V1.07c-.28-.035-.56-.05-.845-.05C4.04 1.02 0 5.06 0 10.02s4.04 9 9 9 9-4.04 9-9V4.355c1.025.755 2.245 1.25 3.59 1.395V0c-.43 0-.845.05-1.245.145-.88.215-1.685.645-2.355 1.23V.02h-5.465z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Columna 2: Navegación -->
            <div class="lg:pl-10">
                <h4 class="text-white font-bold uppercase tracking-[0.2em] text-[10px] mb-8">Navegación</h4>
                <ul class="space-y-4 text-sm font-bold uppercase tracking-wider">
                    <li><a href="#" class="text-white hover:text-yellow-400 transition-colors">Inicio</a></li>
                    <li><a href="#nosotros" class="text-white hover:text-yellow-400 transition-colors">Nosotros</a>
                    </li>
                    <li><a href="#ciclos" class="text-white hover:text-yellow-400 transition-colors">Ciclos</a></li>
                    <li><a href="#docentes" class="text-white hover:text-yellow-400 transition-colors">Docentes</a>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Información de Contacto -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-[0.2em] text-[10px] mb-8">Contacto</h4>
                <ul class="space-y-5 text-sm">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span
                            class="text-white font-medium">{{ $institucion->direccion ?? 'Dirección institucional' }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span
                            class="text-white font-bold tracking-widest">{{ $institucion->whatsapp ?? 'S/N' }}</span>
                    </li>
                    @if ($institucion->correo)
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-white font-medium break-all">{{ $institucion->correo }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Columna 4: Horarios -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-[0.2em] text-[10px] mb-8">Horarios</h4>
                <div class="bg-gray-800/40 p-6 rounded-[2rem] border border-gray-700/50">
                    <p class="text-white text-xs font-bold uppercase mb-2">Atención General</p>
                    <p class="text-blue-400 text-sm font-extrabold mb-4">Lun - Vie: 08:00 - 20:00</p>
                    <p class="text-blue-400 text-sm font-extrabold">Sáb: 08:00 - 13:00</p>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div
            class="pt-10 border-t text-white flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] text-gray-600 uppercase font-bold tracking-[0.3em]">
            <p>&copy; {{ date('Y') }} {{ $institucion->razon_social }}. Todos los derechos reservados.</p>
            <div class="flex gap-6">
                <span class="text-gray-700">Estado: {{ $institucion->estado ? 'Activo' : 'Mantenimiento' }}</span>
            </div>
        </div>
    </div>
</footer>
