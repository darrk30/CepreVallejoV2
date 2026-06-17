@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
<link rel="stylesheet" href="{{ asset('css/rendir-examen.css') }}">
@endpush
<x-filament-panels::page>
    <div class="eo-page">
        <div class="eo-layout re-layout-single">

            {{-- ══════════════════════════════════════════════════
                 1. PANTALLA DE CONFIGURACIÓN
            ══════════════════════════════════════════════════ --}}
            @if($estado_vista === 'configuracion')
            <div class="eo-card re-config-card">
                <div class="eo-card-header">
                    <div class="eo-card-header-icon">
                        @svg('heroicon-o-adjustments-horizontal', 'w-5 h-5')
                    </div>
                    <div class="eo-card-header-text">
                        <div class="eo-card-header-title">Configuración</div>
                        <div class="eo-card-header-sub">{{ $examen->titulo }}</div>
                    </div>
                </div>

                <div class="re-config-body">
                    <div class="re-config-field">
                        <label class="re-config-label">
                            Selecciona la carrera a la que postulas:
                        </label>
                        <select wire:model="carrera_seleccionada_id" class="eo-input re-config-select">
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
                        <span class="re-config-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="re-config-info">
                        <ul>
                            <li><strong>Tiempo límite:</strong> {{ $examen->duracion_minutos }} minutos.</li>
                            <li><strong>Preguntas:</strong> {{ $total_preguntas }}.</li>
                            <li>El temporizador comenzará en el momento que presiones "Iniciar".</li>
                        </ul>
                    </div>

                    <button wire:click="iniciarExamen" class="eo-btn-action eo-btn-primary">
                        @svg('heroicon-o-play', 'w-5 h-5') INICIAR EXAMEN
                    </button>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 2. PANTALLA DEL EXAMEN EN PROGRESO
            ══════════════════════════════════════════════════ --}}
            @elseif($estado_vista === 'en_progreso')
            <div class="re-progreso-wrap">
                <button
                    class="re-btn-flotante-movil"
                    x-data
                    @click.prevent="document.getElementById('seccion-cartilla').scrollIntoView({ behavior: 'smooth' })">
                    @svg('heroicon-o-clipboard-document-check', 'w-6 h-6')
                    <span>Cartilla</span>
                </button>
                {{-- PDF --}}
                <div class="eo-card re-pdf-card">
                    @if($examen->pdf_path)
                    <iframe src="{{ asset('storage/' . $examen->pdf_path) }}#toolbar=0" class="re-pdf-iframe"></iframe>
                    @else
                    <div class="eo-empty re-pdf-empty">
                        @svg('heroicon-o-document-minus', 'w-12 h-12')
                        <p>No hay documento PDF adjunto.</p>
                    </div>
                    @endif
                </div>

                {{-- Cartilla de respuestas --}}
                <div id="seccion-cartilla" class="eo-card re-cartilla-card">
                    <div class="eo-card-header re-cartilla-header">
                        <div class="eo-card-header-text">
                            <div class="eo-card-header-title">Hoja de Respuestas</div>
                        </div>

                        {{-- TIMEMER CON ALPINEJS --}}
                        <div class="re-timer"
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

                <div class="re-cartilla-scroll eo-rank-list-scroll">
                    <table class="re-cartilla-table">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>A</th>
                                <th>B</th>
                                <th>C</th>
                                <th>D</th>
                                <th>E</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= $total_preguntas; $i++)
                                <tr>
                                <td class="re-num-pregunta">
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $letra)
                                @php $marcada = isset($respuestas[$i]) && $respuestas[$i] === $letra; @endphp
                                <td class="re-opcion-td">
                                    <div wire:click="marcarRespuesta({{ $i }}, '{{ $letra }}')"
                                        class="re-opcion-circle {{ $marcada ? 'marcada' : '' }}">
                                        {{ $letra }}
                                    </div>
                                </td>
                                @endforeach
                                </tr>
                                @endfor
                        </tbody>
                    </table>
                </div>

                <div class="re-cartilla-footer">
                    <button
                        class="eo-btn-action eo-btn-primary"
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
        <div class="eo-card re-resultado-card">
            <div class="re-resultado-body">
                @if($resultado_intento->es_aprobado)
                <div class="re-resultado-icon-success">
                    @svg('heroicon-o-check-badge', 'w-64 h-64')
                </div>
                <h2 class="re-resultado-titulo">¡Misión Superada!</h2>
                <p class="re-resultado-sub">
                    Alcanzaste el puntaje mínimo para {{ $resultado_intento->carrera->nombre }}.
                </p>
                @else
                <div class="re-resultado-icon-fail">
                    @svg('heroicon-o-x-circle', 'w-64 h-64')
                </div>
                <h2 class="re-resultado-titulo">Siga intentando</h2>
                <p class="re-resultado-sub">
                    No lograste el puntaje mínimo para {{ $resultado_intento->carrera->nombre }}.
                </p>
                @endif

                @php
                    $minutos = intdiv($resultado_intento->tiempo_utilizado, 60);
                    $segundos = $resultado_intento->tiempo_utilizado % 60;
                @endphp

                <div class="re-resultado-stats">
                    <div>
                        <div class="re-stat-label">Puntaje Obtenido</div>
                        <div class="re-stat-puntaje">
                            {{ number_format($resultado_intento->puntaje_obtenido, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="re-stat-label">Tiempo Utilizado</div>
                        <div class="re-stat-tiempo">
                            {{ $minutos }}<span>min </span>{{ str_pad($segundos, 2, '0', STR_PAD_LEFT) }}<span>seg</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('filament.alumno.pages.examenes-ordinarios') }}" class="eo-btn-action eo-btn-primary" style="margin-top: 10px;">
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