{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las
     clases Tailwind del <div class="eo-page">) para volver al CSS clásico. --}}
{{--
@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
<link rel="stylesheet" href="{{ asset('css/rendir-examen.css') }}">
@endpush
--}}
<x-filament-panels::page>
    <div class="eo-page font-handlee text-[#1a1e35] dark:text-[#f1f2fb]">
        <div class="grid grid-cols-1">

            {{-- ══════════════════════════════════════════════════
                 1. PANTALLA DE CONFIGURACIÓN
            ══════════════════════════════════════════════════ --}}
            @if($estado_vista === 'configuracion')
            <div class="mx-auto w-full max-w-[600px] rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                        @svg('heroicon-o-adjustments-horizontal', 'w-5 h-5')
                    </div>
                    <div>
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Configuración</div>
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ $examen->titulo }}</div>
                    </div>
                </div>

                <div class="p-5">
                    <div class="mb-5">
                        <label class="mb-2 block text-[0.9rem] text-[#4a5068] dark:text-[#b6b9d6]">
                            Selecciona la carrera a la que postulas:
                        </label>
                        <select wire:model="carrera_seleccionada_id" class="w-full border-none bg-transparent py-2.5 text-sm text-[#1a1e35] outline-none dark:text-[#f1f2fb]">
                            <option value="">-- Elige una carrera --</option>
                            @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id }}">
                                {{ $carrera->nombre }}
                                @if($carrera->area)
                                ({{ $carrera->area->value }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('carrera_seleccionada_id')
                        <span class="mt-[5px] block text-[0.8rem] text-[#ff5555]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-5 rounded-lg border border-black/6 bg-[#f5f6fd] p-[15px] text-[0.9rem] text-[#4a5068] dark:border-white/8 dark:bg-[#21233a] dark:text-[#b6b9d6]">
                        <ul class="m-0 list-disc pl-5">
                            <li><strong>Tiempo límite:</strong> {{ $examen->duracion_minutos }} minutos.</li>
                            <li><strong>Preguntas:</strong> {{ $total_preguntas }}.</li>
                            <li>El temporizador comenzará en el momento que presiones "Iniciar".</li>
                        </ul>
                    </div>

                    <button wire:click="iniciarExamen" class="box-border inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border-2 border-[#534ab7] bg-[#534ab7] px-2 py-[5px] text-white no-underline shadow-[0_4px_10px_rgba(99,102,241,0.25)] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#4f46e5] hover:bg-[#4f46e5] hover:shadow-[0_6px_14px_rgba(99,102,241,0.35)] dark:border-[#8b83e8] dark:bg-[#8b83e8]">
                        @svg('heroicon-o-play', 'w-5 h-5') INICIAR EXAMEN
                    </button>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 2. PANTALLA DEL EXAMEN EN PROGRESO
            ══════════════════════════════════════════════════ --}}
            @elseif($estado_vista === 'en_progreso')
            <div class="flex flex-wrap items-start gap-5 max-[900px]:pb-20">
                <button
                    class="hidden max-[900px]:fixed max-[900px]:right-6 max-[900px]:bottom-6 max-[900px]:z-50 max-[900px]:flex max-[900px]:items-center max-[900px]:gap-2 max-[900px]:rounded-full max-[900px]:bg-[#534ab7] max-[900px]:px-5 max-[900px]:py-3 max-[900px]:text-sm max-[900px]:font-semibold max-[900px]:text-white max-[900px]:shadow-[0_4px_14px_rgba(99,102,241,0.4)] max-[900px]:transition-transform active:max-[900px]:scale-95 dark:max-[900px]:bg-[#8b83e8]"
                    x-data
                    @click.prevent="document.getElementById('seccion-cartilla').scrollIntoView({ behavior: 'smooth' })">
                    @svg('heroicon-o-clipboard-document-check', 'w-6 h-6')
                    <span>Cartilla</span>
                </button>
                {{-- PDF --}}
                <div class="h-[80vh] min-w-[300px] flex-1 rounded-2xl border border-[#6366f1]/12 bg-white p-0 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">
                    @if($examen->pdf_path)
                    <iframe src="{{ asset('storage/' . $examen->pdf_path) }}#toolbar=0" class="h-full w-full rounded-2xl border-none"></iframe>
                    @else
                    <div class="flex h-full flex-col items-center justify-center gap-2 p-8 text-sm text-[#8890aa] dark:text-[#82859f]">
                        @svg('heroicon-o-document-minus', 'w-12 h-12')
                        <p>No hay documento PDF adjunto.</p>
                    </div>
                    @endif
                </div>

                {{-- Cartilla de respuestas --}}
                <div id="seccion-cartilla" class="w-[350px] shrink-0 rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Hoja de Respuestas</div>

                        {{-- TIMEMER CON ALPINEJS --}}
                        <div class="flex items-center gap-[5px] font-bold text-[#534ab7] dark:text-[#8b83e8]"
                            x-data="{
                                restante: {{ $examen->duracion_minutos * 60 }},
                                intervalo: null,
                                examenActivo: true,

                                confirmarSalida(e) {
                                    // Si el examen ya terminó, no mostramos la alerta
                                    if (!this.examenActivo) return;

                                    e.preventDefault();
                                    e.returnValue = '¿Seguro que quieres salir? Perderás todo tu avance.';
                                    return e.returnValue;
                                },

                                init() {
                                    this.intervalo = setInterval(() => {
                                        if (this.restante <= 0) {
                                            clearInterval(this.intervalo);
                                            this.examenActivo = false; // Desactivamos la alerta
                                            $wire.finalizarExamen();
                                        } else {
                                            this.restante--;
                                        }
                                    }, 1000);

                                    // Limpieza del intervalo si el componente desaparece
                                    this.$cleanup(() => clearInterval(this.intervalo));
                                },

                                get tiempoFormateado() {
                                    let m = Math.floor(this.restante / 60).toString().padStart(2, '0');
                                    let s = (this.restante % 60).toString().padStart(2, '0');
                                    return m + ':' + s;
                                },

                                get estiloTimer() {
                                    if (this.restante <= 60) return 'color: #ef4444; animation: parpadeo 1s infinite;';
                                    if (this.restante <= 300) return 'color: #ef4444;';
                                    return '';
                                }
                            }"
                            @beforeunload.window="confirmarSalida($event)">
                            @svg('heroicon-o-clock', 'w-4 h-4')
                            <span x-text="tiempoFormateado" :style="estiloTimer">--:--</span>
                    </div>
                    {{-- FIN TIMER ALPINE --}}
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-[15px] [scrollbar-width:thin]">
                    <table class="w-full border-collapse text-center">
                        <thead>
                            <tr>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">Nº</th>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">A</th>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">B</th>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">C</th>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">D</th>
                                <th class="pb-2.5 text-[#4a5068] dark:text-[#b6b9d6]">E</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= $total_preguntas; $i++)
                                <tr class="border-b border-black/6 dark:border-white/8">
                                <td class="py-2 font-bold text-[#8890aa] dark:text-[#82859f]">
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $letra)
                                @php $marcada = isset($respuestas[$i]) && $respuestas[$i] === $letra; @endphp
                                <td class="py-2">
                                    <div wire:click="marcarRespuesta({{ $i }}, '{{ $letra }}')"
                                        class="mx-auto flex h-7 w-7 cursor-pointer items-center justify-center rounded-full border-2 text-[11px] font-bold transition-all duration-200 select-none
                                            {{ $marcada
                                                ? 'border-[#534ab7] bg-[#534ab7] text-white dark:border-[#8b83e8] dark:bg-[#8b83e8]'
                                                : 'border-[#4a5068] bg-transparent text-[#4a5068] hover:border-[#534ab7] hover:text-[#534ab7] dark:border-[#b6b9d6] dark:text-[#b6b9d6] dark:hover:border-[#8b83e8] dark:hover:text-[#8b83e8]' }}">
                                        {{ $letra }}
                                    </div>
                                </td>
                                @endforeach
                                </tr>
                                @endfor
                        </tbody>
                    </table>
                </div>

                <div class="mt-2.5 border-t border-black/6 pt-[15px] dark:border-white/8">
                    <button
                        class="box-border inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border-2 border-[#534ab7] bg-[#534ab7] px-2 py-[5px] text-white no-underline shadow-[0_4px_10px_rgba(99,102,241,0.25)] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#4f46e5] hover:bg-[#4f46e5] hover:shadow-[0_6px_14px_rgba(99,102,241,0.35)] dark:border-[#8b83e8] dark:bg-[#8b83e8]"
                        x-on:click="if(confirm('¿Estás seguro de enviar tus respuestas? No podrás modificarlas después.')) { examenActivo = false; $wire.finalizarExamen() }">
                        @svg('heroicon-o-paper-airplane', 'w-5 h-5') ENVIAR RESPUESTAS
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
                 3. PANTALLA DE RESULTADOS
            ══════════════════════════════════════════════════ --}}
        @elseif($estado_vista === 'finalizado')
        <div class="mx-auto w-full max-w-[600px] rounded-2xl border border-[#6366f1]/12 bg-white p-6 text-center shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">
            <div class="px-5 py-10">
                @if($resultado_intento->es_aprobado)
                <div class="mb-5 flex justify-center text-emerald-500">
                    @svg('heroicon-o-check-badge', 'w-64 h-64')
                </div>
                <h2 class="mb-[5px] text-[2rem] text-[#4a5068] dark:text-[#b6b9d6]">¡Misión Superada!</h2>
                <p class="text-[#4a5068] dark:text-[#b6b9d6]">
                    Alcanzaste el puntaje mínimo para {{ $resultado_intento->carrera->nombre }}.
                </p>
                @else
                <div class="mb-5 flex justify-center text-red-500">
                    @svg('heroicon-o-x-circle', 'w-64 h-64')
                </div>
                <h2 class="mb-[5px] text-[2rem] text-[#4a5068] dark:text-[#b6b9d6]">Siga intentando</h2>
                <p class="text-[#4a5068] dark:text-[#b6b9d6]">
                    No lograste el puntaje mínimo para {{ $resultado_intento->carrera->nombre }}.
                </p>
                @endif

                @php
                    $minutos = intdiv($resultado_intento->tiempo_utilizado, 60);
                    $segundos = $resultado_intento->tiempo_utilizado % 60;
                @endphp

                <div class="mt-[30px] flex justify-around rounded-xl border border-[#6366f1]/12 bg-[#f5f6fd] p-5 dark:border-[#9482ff]/18 dark:bg-[#21233a]">
                    <div>
                        <div class="text-[0.8rem] text-[#4a5068] uppercase dark:text-[#b6b9d6]">Puntaje Obtenido</div>
                        <div class="text-[2rem] font-bold text-[#534ab7] dark:text-[#8b83e8]">
                            {{ number_format($resultado_intento->puntaje_obtenido, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[0.8rem] text-[#4a5068] uppercase dark:text-[#b6b9d6]">Tiempo Utilizado</div>
                        <div class="text-[2rem] font-bold text-[#4a5068] dark:text-[#b6b9d6]">
                            {{ $minutos }}<span class="text-base">min </span>{{ str_pad($segundos, 2, '0', STR_PAD_LEFT) }}<span class="text-base">seg</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('filament.alumno.pages.examenes-ordinarios') }}" wire:navigate class="mt-2.5 box-border inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border-2 border-[#534ab7] bg-[#534ab7] px-2 py-[5px] text-white no-underline shadow-[0_4px_10px_rgba(99,102,241,0.25)] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#4f46e5] hover:bg-[#4f46e5] hover:shadow-[0_6px_14px_rgba(99,102,241,0.35)] dark:border-[#8b83e8] dark:bg-[#8b83e8]">
                    @svg('heroicon-o-arrow-left', 'w-5 h-5') Volver
                </a>
            </div>
        </div>
        @endif

    </div>
    </div>

    <style>
        @keyframes parpadeo {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }
    </style>
</x-filament-panels::page>
