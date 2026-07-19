{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las
     clases Tailwind del <div class="eo-page">) para volver al CSS clásico. --}}
{{--
@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
<link rel="stylesheet" href="{{ asset('css/mis-intentos.css') }}">
@endpush
--}}
<x-filament-panels::page>
    <div class="eo-page font-handlee text-[#1a1e35] dark:text-[#f1f2fb]">
        <div class="grid grid-cols-[320px_1fr] items-start gap-5 max-[768px]:grid-cols-1">

            {{-- ══════════════════════════════════════════════════
                 PANEL IZQUIERDO — lista de exámenes
            ══════════════════════════════════════════════════ --}}
            <div class="sticky top-5 rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                        @svg('heroicon-o-academic-cap', 'w-5 h-5')
                    </div>
                    <div>
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Mis Exámenes</div>
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ count($examenes) }} con intentos</div>
                    </div>
                </div>

                @if(empty($examenes))
                <div class="flex flex-col items-center gap-2 p-8 text-sm text-[#8890aa] dark:text-[#82859f]">
                    @svg('heroicon-o-inbox', 'w-10 h-10')
                    <span>Aún no has rendido ningún examen.</span>
                </div>
                @else
                <ul class="m-0 flex list-none flex-col gap-1.5 p-0">
                    @foreach($examenes as $examen)
                    <li
                        wire:click="seleccionarExamen({{ $examen['id'] }})"
                        class="flex cursor-pointer items-center gap-3 rounded-[10px] border px-3.5 py-3 transition-all duration-200
                            {{ $examenId === $examen['id']
                                ? 'border-[#534ab7] bg-[#6366f1]/12 dark:border-[#8b83e8] dark:bg-[#8b83e8]/12'
                                : 'border-transparent hover:border-black/6 hover:bg-[#f5f6fd] dark:hover:border-white/8 dark:hover:bg-[#21233a]' }}">
                        <div class="shrink-0 text-[#534ab7] dark:text-[#8b83e8]">
                            @svg('heroicon-o-document-text', 'w-5 h-5')
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">{{ $examen['titulo'] }}</div>
                            <div class="mt-0.5 flex items-center gap-1 text-xs text-[#4a5068] dark:text-[#b6b9d6]">
                                @svg('heroicon-o-arrow-path', 'w-3 h-3')
                                {{ $examen['intentos'] }} {{ $examen['intentos'] === 1 ? 'intento' : 'intentos' }}
                            </div>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 shrink-0 text-[#8890aa] dark:text-[#82859f]')
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════════
                 PANEL DERECHO — intentos del examen seleccionado
            ══════════════════════════════════════════════════ --}}
            <div class="min-h-[300px] rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                @if(!$examenId)
                {{-- Sin selección --}}
                <div class="flex flex-col items-center justify-center gap-3 px-5 py-[60px] text-center text-[#8890aa] dark:text-[#82859f]">
                    @svg('heroicon-o-cursor-arrow-rays', 'w-16 h-16')
                    <p class="text-[0.9rem] leading-normal text-[#4a5068] dark:text-[#b6b9d6]">Selecciona un examen para<br>ver tu historial de intentos</p>
                </div>

                @else
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                        @svg('heroicon-o-clock', 'w-5 h-5')
                    </div>
                    <div>
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Historial de Intentos</div>
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ Str::limit($examenTitulo, 40) }}</div>
                    </div>
                </div>

                @if($examenBloqueado)
                <div class="mb-4 flex items-start gap-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-[0.85rem] text-amber-700 dark:text-amber-400">
                    @svg('heroicon-o-lock-closed', 'w-5 h-5 shrink-0 mt-0.5')
                    <span>Ya viste el detalle de respuestas de este examen, por lo que ya no puedes volver a rendirlo.</span>
                </div>
                @endif

                @if(empty($intentos))
                <div class="flex flex-col items-center justify-center gap-3 px-5 py-[60px] text-center text-[#8890aa] dark:text-[#82859f]">
                    @svg('heroicon-o-inbox', 'w-12 h-12')
                    <p class="text-[0.9rem] leading-normal text-[#4a5068] dark:text-[#b6b9d6]">No hay intentos para este examen.</p>
                </div>
                @else
                <div class="flex flex-col gap-3.5 py-1">
                    @foreach($intentos as $idx => $intento)
                    <div class="rounded-xl border border-black/6 bg-[#f5f6fd] px-5 py-4 transition-colors dark:border-white/8 dark:bg-[#21233a]
                        {{ $intento['aprobado'] ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-red-500' }}">

                        {{-- Número de intento + badge --}}
                        <div class="mb-3.5 flex items-center justify-between">
                            <span class="text-[0.95rem] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">Intento #{{ count($intentos) - $idx }}</span>
                            @if($intento['aprobado'])
                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-500/15 px-2.5 py-[3px] text-xs font-semibold text-emerald-500">
                                @svg('heroicon-o-check-circle', 'w-3 h-3') Aprobado
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-full border border-red-500/30 bg-red-500/15 px-2.5 py-[3px] text-xs font-semibold text-red-500">
                                @svg('heroicon-o-x-circle', 'w-3 h-3') No aprobado
                            </span>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="mb-4 grid grid-cols-4 gap-3 max-[600px]:grid-cols-2">
                            <div class="flex flex-col gap-1">
                                <div class="text-[0.7rem] tracking-wider text-[#8890aa] uppercase dark:text-[#82859f]">Puntaje</div>
                                <div class="text-[1.2rem] font-bold {{ $intento['aprobado'] ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ number_format($intento['puntaje'], 2) }}
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="text-[0.7rem] tracking-wider text-[#8890aa] uppercase dark:text-[#82859f]">Carrera</div>
                                <div class="text-[0.85rem] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">{{ $intento['carrera'] }}</div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="text-[0.7rem] tracking-wider text-[#8890aa] uppercase dark:text-[#82859f]">Tiempo</div>
                                <div class="text-[1.2rem] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">
                                    {{ $intento['tiempo_min'] }}<span class="text-xs font-normal text-[#4a5068] dark:text-[#b6b9d6]">m</span>
                                    {{ $intento['tiempo_seg'] }}<span class="text-xs font-normal text-[#4a5068] dark:text-[#b6b9d6]">s</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="text-[0.7rem] tracking-wider text-[#8890aa] uppercase dark:text-[#82859f]">Fecha</div>
                                <div class="text-[0.85rem] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">{{ $intento['fecha'] }}</div>
                            </div>
                        </div>

                        {{-- Acción --}}
                        <div class="flex flex-wrap items-center gap-3">
                            @php
                                $btnDetalle = 'box-border inline-flex h-9 max-w-[300px] items-center gap-1.5 rounded-lg border border-[#6366f1]/12 bg-transparent px-4 text-[0.8rem] text-[#4a5068] no-underline transition-all duration-200 hover:border-[#534ab7] hover:bg-[#6366f1]/8 hover:text-[#534ab7] dark:border-[#9482ff]/18 dark:text-[#b6b9d6] dark:hover:border-[#8b83e8] dark:hover:bg-[#8b83e8]/8 dark:hover:text-[#8b83e8]';
                            @endphp
                            <button
                                type="button"
                                @if($examenBloqueado)
                                    wire:click="verDetalle({{ $intento['id'] }})"
                                @else
                                    wire:click="mountAction('confirmVerDetalle', { intentoId: {{ $intento['id'] }} })"
                                @endif
                                class="{{ $btnDetalle }}">
                                @svg('heroicon-o-magnifying-glass', 'w-4 h-4 shrink-0')
                                <span class="truncate whitespace-nowrap">Ver detalle</span>
                            </button>

                            @if($intento['pdf_path'])
                            <a href="{{ asset('storage/' . $intento['pdf_path']) }}"
                                target="_blank"
                                download
                                class="{{ $btnDetalle }}"
                                x-data="{ descargando: false }"
                                @click="descargando = true; setTimeout(() => descargando = false, 1000)">

                                {{-- Estado Normal --}}
                                <span class="inline-flex max-w-full items-center gap-1.5" x-show="!descargando">
                                    <span class="truncate whitespace-nowrap">{{ basename($intento['pdf_path']) }}</span>
                                </span>

                                {{-- Estado Descargando --}}
                                <span class="inline-flex max-w-full items-center gap-1.5" x-show="descargando" x-cloak>
                                    <span class="truncate whitespace-nowrap">Descargando...</span>
                                </span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             MODAL — detalle pregunta por pregunta
        ══════════════════════════════════════════════════════ --}}
        @if($modalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[#141628]/35 p-4" wire:click.self="cerrarModal">
            <div class="flex max-h-[85vh] w-[95vw] max-w-[700px] flex-col overflow-hidden rounded-2xl border border-[#6366f1]/12 bg-white shadow-[0_8px_32px_rgba(99,102,241,0.12)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                <div class="relative flex w-full items-start pt-2 pr-12">
                    <div class="mt-2.5 flex w-full items-center justify-between gap-[30px] max-[640px]:flex-col max-[640px]:items-start max-[640px]:gap-4">

                        {{-- COLUMNA IZQUIERDA: Textos y Puntos --}}
                        <div class="flex-1">
                            <span class="text-[15px] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">Detalle del Intento</span>
                            <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ $modalExamenTitulo }}</div>
                            <div class="mt-1.5 flex items-center gap-2.5">
                                <span class="text-[1.4rem] font-bold text-[#534ab7] dark:text-[#8b83e8]">{{ number_format($modalPuntaje, 2) }} pts</span>
                                @if($modalAprobado)
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-500/15 px-2.5 py-[3px] text-xs font-semibold text-emerald-500">
                                    @svg('heroicon-o-check-circle', 'w-3 h-3') Aprobado
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full border border-red-500/30 bg-red-500/15 px-2.5 py-[3px] text-xs font-semibold text-red-500">
                                    @svg('heroicon-o-x-circle', 'w-3 h-3') No aprobado
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA: Barra de progreso --}}
                        <div class="w-full flex-1 max-w-[320px] max-[640px]:max-w-full">
                            @php
                            $porcentaje = $modalPuntajeMinimo > 0
                            ? min(100, round(($modalPuntaje / $modalPuntajeMinimo) * 100, 1))
                            : 0;
                            @endphp
                            <div class="w-full">
                                <div class="mb-1.5 flex justify-between text-[0.78rem] text-[#4a5068] dark:text-[#b6b9d6]">
                                    <span>Área: <strong>{{ $modalAreaNombre }}</strong></span>
                                    <span>Mínimo: <strong>{{ number_format($modalPuntajeMinimo, 2) }} pts</strong></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-black/6 dark:bg-white/8">
                                    <div
                                        class="h-full rounded-full transition-[width] duration-[600ms] ease-in-out {{ $modalAprobado ? 'bg-emerald-500' : 'bg-red-500' }}"
                                        style="width: {{ $porcentaje }}%">
                                    </div>
                                </div>
                                <div class="mt-1 text-right text-[0.72rem] text-[#8890aa] dark:text-[#82859f]">{{ $porcentaje }}% del mínimo requerido</div>
                            </div>
                        </div>

                    </div>

                    <button wire:click="cerrarModal" class="absolute top-0 right-0 z-10 m-[5px] flex h-[30px] w-[30px] items-center justify-center rounded-lg border border-[#6366f1]/12 bg-[#f5f6fd] text-[#4a5068] transition-colors duration-200 hover:bg-[#6366f1]/10 hover:text-[#534ab7] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#b6b9d6] dark:hover:bg-[#8b83e8]/18 dark:hover:text-[#8b83e8]">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>

                {{-- Leyenda --}}
                <div class="mb-1 flex gap-4 border-b border-black/6 p-2.5 dark:border-white/8">
                    <span class="flex items-center gap-1.5 text-[0.8rem] text-[#4a5068] dark:text-[#b6b9d6]">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Correcta
                    </span>
                    <span class="flex items-center gap-1.5 text-[0.8rem] text-[#4a5068] dark:text-[#b6b9d6]">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-500"></span> Incorrecta
                    </span>
                    <span class="flex items-center gap-1.5 text-[0.8rem] text-[#4a5068] dark:text-[#b6b9d6]">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-[#8890aa] dark:bg-[#82859f]"></span> Sin marcar
                    </span>
                </div>

                {{-- Tabla de respuestas --}}
                <div class="max-h-[55vh] overflow-y-auto [scrollbar-width:thin]">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr>
                                <th class="sticky top-0 border-b border-black/6 bg-white px-3 py-2.5 text-left text-xs tracking-wider text-[#4a5068] uppercase dark:border-white/8 dark:bg-[#1a1c2c] dark:text-[#b6b9d6]">N°</th>
                                <th class="sticky top-0 border-b border-black/6 bg-white px-3 py-2.5 text-left text-xs tracking-wider text-[#4a5068] uppercase dark:border-white/8 dark:bg-[#1a1c2c] dark:text-[#b6b9d6]">Asignatura</th>
                                <th class="sticky top-0 border-b border-black/6 bg-white px-3 py-2.5 text-center text-xs tracking-wider text-[#4a5068] uppercase dark:border-white/8 dark:bg-[#1a1c2c] dark:text-[#b6b9d6]">Tu Resp.</th>
                                <th class="sticky top-0 border-b border-black/6 bg-white px-3 py-2.5 text-center text-xs tracking-wider text-[#4a5068] uppercase dark:border-white/8 dark:bg-[#1a1c2c] dark:text-[#b6b9d6]">Correcta</th>
                                <th class="sticky top-0 border-b border-black/6 bg-white px-3 py-2.5 text-right text-xs tracking-wider text-[#4a5068] uppercase dark:border-white/8 dark:bg-[#1a1c2c] dark:text-[#b6b9d6]">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modalDetalle as $fila)
                            <tr class="border-b border-black/6 transition-colors hover:bg-[#f5f6fd] dark:border-white/8 dark:hover:bg-[#21233a] {{ $fila['en_blanco'] ? 'bg-black/2 dark:bg-white/3' : ($fila['es_correcta'] ? 'bg-emerald-500/5' : 'bg-red-500/5') }}">
                                <td class="w-12 px-3 py-2.5 font-bold text-[#8890aa] dark:text-[#82859f]">{{ str_pad($fila['numero'], 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-3 py-2.5 text-[0.8rem] text-[#4a5068] dark:text-[#b6b9d6]">{{ ucfirst(str_replace('_', ' ', $fila['asignatura'])) }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($fila['en_blanco'])
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-transparent bg-[#8890aa] text-[0.8rem] font-bold text-white dark:bg-[#82859f]" title="Sin marcar">
                                        —
                                    </span>
                                    @else
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-transparent text-[0.8rem] font-bold {{ $fila['es_correcta'] ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                                        {{ $fila['marcada'] }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-emerald-500 bg-transparent text-[0.8rem] font-bold text-emerald-500">
                                        {{ $fila['correcta'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right font-bold {{ $fila['puntos'] > 0 ? 'text-emerald-500' : ($fila['puntos'] < 0 ? 'text-red-500' : 'text-[#8890aa] dark:text-[#82859f]') }}">
                                    {{ $fila['puntos'] > 0 ? '+' : '' }}{{ number_format($fila['puntos'], 3) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        @endif

    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
