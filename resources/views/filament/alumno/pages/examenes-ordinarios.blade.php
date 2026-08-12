{{-- Migrado a Tailwind. Si algo falla, descomenta este bloque (y quita las clases Tailwind del <div class="eo-page">) para volver al CSS clásico. --}}
{{--
@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
@endpush
--}}
<x-filament-panels::page>

    <div class="eo-page min-h-[70vh] font-handlee text-[#1a1e35] dark:text-[#f1f2fb]">
        <div class="eo-layout mx-auto grid max-w-[1300px] grid-cols-[1fr_380px] gap-6 max-[900px]:grid-cols-1">

            {{-- ══════════════════════════════════════════════════════
                 PANEL IZQUIERDO — quest log de exámenes
            ══════════════════════════════════════════════════════ --}}
            <div class="rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                        @svg('heroicon-o-academic-cap', 'w-5 h-5')
                    </div>
                    <div>
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Exámenes disponibles</div>
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ count($examenes) }} exámenes activos</div>
                    </div>
                </div>

                <div class="mb-4 flex items-center gap-2 rounded-[10px] border border-[#6366f1]/12 bg-[#f5f6fd] px-3 text-[#8890aa] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#82859f]">
                    <i class="ti ti-search"></i>
                    <input
                        type="text"
                        wire:model.live="examSearch"
                        placeholder="Buscar misión..."
                        class="flex-1 border-none bg-transparent py-2.5 text-sm text-[#1a1e35] outline-none placeholder:text-[#8890aa] dark:text-[#f1f2fb] dark:placeholder:text-[#82859f]" />
                </div>

                <div class="flex flex-col gap-3">
                    @php
                    $examenesVista = collect($examenes);
                    if ($examSearch) {
                    $q = mb_strtolower($examSearch);
                    $examenesVista = $examenesVista->filter(
                    fn($e) => str_contains(mb_strtolower($e['titulo']), $q)
                    )->values();
                    }
                    @endphp

                    @forelse($examenesVista as $examen)
                    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-xl border px-[1.1rem] py-4 transition-colors duration-200 max-[640px]:grid-cols-[auto_1fr] max-[640px]:items-start
                        {{ $examenId === $examen['id']
                            ? 'border-[#534ab7] bg-[#6366f1]/10 dark:border-[#8b83e8] dark:bg-[#8b83e8]/18'
                            : 'border-[#6366f1]/12 bg-[#f5f6fd] hover:border-[#534ab7] hover:bg-[#6366f1]/10 dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:hover:border-[#8b83e8] dark:hover:bg-[#8b83e8]/18' }}">

                        <div class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-lg bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                            @svg('heroicon-o-document-text', 'w-5 h-5')
                        </div>

                        <div class="cursor-pointer" wire:click="seleccionarExamen({{ $examen['id'] }})">
                            <div class="text-sm font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">{{ $examen['titulo'] }}</div>
                            <div class="mt-[3px] flex flex-wrap items-center gap-1 text-xs text-[#4a5068] dark:text-[#b6b9d6]">
                                <span class="inline-flex items-center gap-[3px] rounded-md bg-[#6366f1]/10 px-2 py-0.5 text-[11px] font-semibold text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                                    @svg('heroicon-o-clock', 'h-3.5 w-3.5 mr-0.5 align-middle') {{ $examen['duracion'] }} min
                                </span>

                                <span class="text-[#8890aa] dark:text-[#82859f]">·</span>

                                <span class="inline-flex items-center gap-1">
                                    @svg('heroicon-o-list-bullet', 'h-3.5 w-3.5 mr-0.5 align-middle') {{ $examen['preguntas'] }} preg.
                                </span>

                                <span class="text-[#8890aa] dark:text-[#82859f]">·</span>

                                <span class="inline-flex items-center gap-1">
                                    @svg('heroicon-o-calendar', 'h-3.5 w-3.5 mr-0.5 align-middle') {{ $examen['fecha'] }}
                                </span>
                            </div>
                        </div>

                        <div class="flex w-full items-center gap-3 max-[640px]:col-span-full max-[640px]:mt-3">
                            <!-- Botón Secundario: Ver Intentos -->
                            <button class="box-border inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-[10px] border-2 border-[#6366f1]/12 bg-transparent px-2 py-[5px] text-[#4a5068] no-underline transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#534ab7] hover:bg-[#f8fafc] hover:text-[#534ab7] hover:shadow-[0_4px_12px_rgba(0,0,0,0.05)] dark:border-[#9482ff]/18 dark:text-[#b6b9d6] dark:hover:border-[#8b83e8] dark:hover:text-[#8b83e8]"
                                title="Ver historial de intentos"
                                wire:click="irAIntentos({{ $examen['id'] }})">
                                @svg('heroicon-o-clipboard-document-list', 'w-5 h-5')
                                <span class="whitespace-nowrap">Intentos</span>
                            </button>

                            <!-- Botón Primario: Iniciar Examen -->
                            <a href="{{ \App\Filament\Alumno\Pages\RendirExamen::getUrl(['examen_id' => $examen['id']]) }}"
                                wire:navigate
                                class="box-border inline-flex h-10 flex-[2] items-center justify-center gap-2 rounded-[10px] border-2 border-[#534ab7] bg-[#534ab7] px-2 py-[5px] text-white no-underline shadow-[0_4px_10px_rgba(99,102,241,0.25)] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#4f46e5] hover:bg-[#4f46e5] hover:shadow-[0_6px_14px_rgba(99,102,241,0.35)] dark:border-[#8b83e8] dark:bg-[#8b83e8]"
                                title="Iniciar Misión / Rendir Examen">
                                @svg('heroicon-o-rocket-launch', 'w-5 h-5')
                                <span class="whitespace-nowrap">Iniciar</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center gap-2 p-8 text-sm text-[#8890aa] dark:text-[#82859f]">
                        <i class="ti ti-ghost text-[32px]"></i>
                        <span>No se encontraron misiones.</span>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════
                 PANEL DERECHO — leaderboard
            ══════════════════════════════════════════════════════ --}}
            <div class="rounded-2xl border border-[#6366f1]/12 bg-white p-6 shadow-[0_1px_4px_rgba(99,102,241,0.06)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                        @svg('heroicon-o-trophy', 'w-5 h-5')
                    </div>
                    <div>
                        <div class="text-[15px] leading-[1.2] font-semibold text-[#1a1e35] dark:text-[#f1f2fb]">Tabla de clasificación</div>
                        @if($examenTitulo)
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ Str::limit($examenTitulo, 32) }}</div>
                        @else
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">Selecciona un examen</div>
                        @endif
                    </div>
                </div>

                {{-- Filtro carrera --}}
                @if($examenId && !empty($carreras))
                <div class="mb-4">
                    <select wire:model.live="carreraFiltro"
                        wire:change="filtrarCarrera($event.target.value)"
                        class="w-full cursor-pointer rounded-[10px] border border-[#6366f1]/12 bg-[#f5f6fd] px-3 py-2 text-[13px] text-[#1a1e35] outline-none focus:border-[#534ab7] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#f1f2fb] dark:focus:border-[#8b83e8]">
                        <option value="">Todas las carreras</option>
                        @foreach($carreras as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Sin examen --}}
                @if(! $examenId)
                <div class="flex flex-col items-center gap-2.5 py-12 px-4 text-center text-sm leading-relaxed text-[#8890aa] dark:text-[#82859f]">
                    <i class="ti ti-tournament text-[36px] opacity-50"></i>
                    <p>Elige un examen para<br>ver el ranking de los alumnos</p>
                </div>

                {{-- Sin intentos --}}
                @elseif(empty($ranking))
                <div class="flex flex-col items-center gap-2.5 py-12 px-4 text-center text-sm leading-relaxed text-[#8890aa] dark:text-[#82859f]">
                    <i class="ti ti-mood-empty text-[36px] opacity-50"></i>
                    <p>Aún no hay campeones<br>para esta misión. ¡Sé el primero!</p>
                </div>

                {{-- Ranking con datos --}}
                @else

                @php
                $top1 = $ranking[0] ?? null;
                $top2 = $ranking[1] ?? null;
                $top3 = $ranking[2] ?? null;
                $rest = array_slice($ranking, 3);

                // Posición del usuario en el ranking completo
                $myPos = null;
                foreach ($ranking as $idx => $r) {
                if ($r['es_yo']) { $myPos = $idx + 1; break; }
                }
                @endphp

                {{-- PODIO TOP 3 --}}
                <div class="mb-4 flex items-end justify-center gap-2 pt-4 pb-2 max-[480px]:gap-1">

                    {{-- Plata (#2) --}}
                    @if($top2)
                    <div class="flex max-w-[110px] flex-1 flex-col items-center gap-1">
                        <div class="relative">
                            @php $isMe2 = $top2['es_yo']; @endphp
                            <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 text-sm font-bold
                                {{ $isMe2
                                    ? 'border-[#534ab7]! bg-[#6366f1]/10! text-[#534ab7]! shadow-[0_0_0_4px_rgba(83,74,183,0.15)] dark:border-[#8b83e8]! dark:bg-[#8b83e8]/18! dark:text-[#8b83e8]!'
                                    : 'border-slate-400 bg-slate-100 text-slate-600' }}">
                                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top2['nombre']), 0, 2)))) }}
                            </div>
                            @if($top2['es_yo'])
                            <div class="absolute -bottom-2 left-1/2 z-10 -translate-x-1/2 rounded-xl border-2 border-white bg-[#534ab7] px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-white shadow-[0_2px_6px_rgba(83,74,183,0.4)] dark:bg-[#8b83e8]">TÚ</div>
                            @endif
                        </div>
                        <div class="mt-1 w-full max-w-[90px] cursor-default truncate text-center text-[13px] font-semibold max-[480px]:text-[11px] {{ $top2['es_yo'] ? 'text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#1a1e35] dark:text-[#f1f2fb]' }}" title="{{ $top2['nombre'] }}">
                            {{ $top2['nombre'] }}
                        </div>
                        <div class="text-lg font-semibold text-[#4a5068] dark:text-[#b6b9d6]">{{ number_format($top2['puntaje']) }}</div>
                        <div class="mt-1 flex h-10 w-full items-start justify-center rounded-t-lg bg-slate-100 pt-1.5 text-xs font-bold text-slate-600">#2</div>
                    </div>
                    @endif

                    {{-- Oro (#1) --}}
                    @if($top1)
                    <div class="flex max-w-[110px] flex-1 flex-col items-center gap-1">
                        <div class="relative">
                            <div class="absolute -top-[18px] left-1/2 -translate-x-1/2 text-base">👑</div>
                            @php $isMe1 = $top1['es_yo']; @endphp
                            <div class="flex h-14 w-14 items-center justify-center rounded-full border-2 text-base font-bold
                                {{ $isMe1
                                    ? 'border-[#534ab7]! bg-[#6366f1]/10! text-[#534ab7]! shadow-[0_0_0_4px_rgba(83,74,183,0.15)] dark:border-[#8b83e8]! dark:bg-[#8b83e8]/18! dark:text-[#8b83e8]!'
                                    : 'border-amber-400 bg-amber-100 text-amber-800' }}">
                                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top1['nombre']), 0, 2)))) }}
                            </div>
                            @if($top1['es_yo'])
                            <div class="absolute -bottom-2 left-1/2 z-10 -translate-x-1/2 rounded-xl border-2 border-white bg-[#534ab7] px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-white shadow-[0_2px_6px_rgba(83,74,183,0.4)] dark:bg-[#8b83e8]">TÚ</div>
                            @endif
                        </div>
                        <div class="mt-1 w-full max-w-[90px] cursor-default truncate text-center text-[13px] font-semibold max-[480px]:text-[11px] {{ $top1['es_yo'] ? 'text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#1a1e35] dark:text-[#f1f2fb]' }}" title="{{ $top1['nombre'] }}">
                            {{ $top1['nombre'] }}
                        </div>
                        <div class="text-lg font-semibold text-[#4a5068] dark:text-[#b6b9d6]">{{ number_format($top1['puntaje']) }}</div>
                        <div class="mt-1 flex h-14 w-full items-start justify-center rounded-t-lg bg-amber-100 pt-2 text-xs font-bold text-amber-800">#1</div>
                    </div>
                    @endif

                    {{-- Bronce (#3) --}}
                    @if($top3)
                    <div class="flex max-w-[110px] flex-1 flex-col items-center gap-1">
                        <div class="relative">
                            @php $isMe3 = $top3['es_yo']; @endphp
                            <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 text-sm font-bold
                                {{ $isMe3
                                    ? 'border-[#534ab7]! bg-[#6366f1]/10! text-[#534ab7]! shadow-[0_0_0_4px_rgba(83,74,183,0.15)] dark:border-[#8b83e8]! dark:bg-[#8b83e8]/18! dark:text-[#8b83e8]!'
                                    : 'border-[#d97706] bg-[#fdf2e9] text-amber-800' }}">
                                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top3['nombre']), 0, 2)))) }}
                            </div>
                            @if($top3['es_yo'])
                            <div class="absolute -bottom-2 left-1/2 z-10 -translate-x-1/2 rounded-xl border-2 border-white bg-[#534ab7] px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-white shadow-[0_2px_6px_rgba(83,74,183,0.4)] dark:bg-[#8b83e8]">TÚ</div>
                            @endif
                        </div>
                        <div class="mt-1 w-full max-w-[90px] cursor-default truncate text-center text-[13px] font-semibold max-[480px]:text-[11px] {{ $top3['es_yo'] ? 'text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#1a1e35] dark:text-[#f1f2fb]' }}" title="{{ $top3['nombre'] }}">
                            {{ $top3['nombre'] }}
                        </div>
                        <div class="text-lg font-semibold text-[#4a5068] dark:text-[#b6b9d6]">{{ number_format($top3['puntaje']) }}</div>
                        <div class="mt-1 flex h-7 w-full items-start justify-center rounded-t-lg bg-[#fdf2e9] pt-1 text-xs font-bold text-amber-800">#3</div>
                    </div>
                    @endif

                </div>

                {{-- Mi posición si NO estoy en top 3 --}}
                @php $myItem = collect($ranking)->firstWhere('es_yo', true); @endphp
                @if($myItem && $myPos > 3)
                <div class="mb-3 flex items-center justify-between rounded-[10px] border border-[#6366f1]/25 bg-[#6366f1]/10 px-3.5 py-2.5 text-[13px] font-semibold text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">
                    <span>Tu posición</span>
                    <div class="flex items-center gap-2">
                        <span>#{{ $myPos }}</span>
                        <strong>{{ number_format($myItem['puntaje']) }} pts</strong>
                    </div>
                </div>
                @endif

                {{-- Lista 4 en adelante --}}
                @if(!empty($rest))
                <ul class="mb-4 flex list-none flex-col gap-0.5 p-0">
                    @foreach($rest as $i => $item)
                    @php $pos = $i + 4; @endphp
                    <li class="flex items-center gap-2.5 rounded-[10px] px-2 py-2.5 transition-colors duration-150 {{ $item['es_yo'] ? 'bg-[#6366f1]/10 dark:bg-[#8b83e8]/18' : 'hover:bg-[#f5f6fd] dark:hover:bg-[#21233a]' }}">
                        <span class="w-7 shrink-0 text-center text-xs font-semibold text-[#8890aa] dark:text-[#82859f]">#{{ $pos }}</span>
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-[11px] font-bold
                            {{ $item['es_yo']
                                ? 'border-[#6366f1]/30 bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]'
                                : 'border-[#6366f1]/12 bg-[#f5f6fd] text-[#4a5068] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#b6b9d6]' }}">
                            {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $item['nombre']), 0, 2)))) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-medium {{ $item['es_yo'] ? 'font-semibold text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#1a1e35] dark:text-[#f1f2fb]' }}">
                                {{ $item['nombre'] }}
                                @if($item['es_yo'])<span class="ml-1 rounded bg-[#6366f1]/10 px-1.5 py-px text-[11px] font-medium text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">(tú)</span>@endif
                            </div>
                            <div class="truncate text-[11px] {{ $item['es_yo'] ? 'text-[#534ab7]/75 dark:text-[#8b83e8]/75' : 'text-[#8890aa] dark:text-[#82859f]' }}">{{ $item['carrera'] }}</div>
                        </div>
                        <div class="shrink-0 text-[13px] font-semibold {{ $item['es_yo'] ? 'text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#4a5068] dark:text-[#b6b9d6]' }}">
                            {{ number_format($item['puntaje']) }} pts
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif

                <div class="mt-3">
                    <button wire:click="abrirModal" class="box-border inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border-2 border-[#6366f1]/12 bg-transparent px-2 py-[5px] text-[#4a5068] no-underline transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:border-[#534ab7] hover:bg-[#f8fafc] hover:text-[#534ab7] hover:shadow-[0_4px_12px_rgba(0,0,0,0.05)] dark:border-[#9482ff]/18 dark:text-[#b6b9d6] dark:hover:border-[#8b83e8] dark:hover:text-[#8b83e8]">
                        Ver lista completa <i class="ti ti-list" aria-hidden="true"></i>
                    </button>
                </div>

                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             MODAL — ranking completo
        ══════════════════════════════════════════════════════════ --}}
        @if($modalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[#141628]/35 p-4" wire:click.self="$set('modalOpen', false)">
            <div class="flex max-h-[85vh] w-full max-w-[520px] flex-col overflow-hidden rounded-2xl border border-[#6366f1]/12 bg-white shadow-[0_8px_32px_rgba(99,102,241,0.12)] dark:border-[#9482ff]/18 dark:bg-[#1a1c2c]">

                <div class="flex shrink-0 items-start justify-between border-b border-black/6 px-6 py-5 dark:border-white/8">
                    <div>
                        <span class="text-[15px] font-bold text-[#1a1e35] dark:text-[#f1f2fb]">Ranking completo</span>
                        @if($carreraFiltro)
                        <span class="text-[13px] font-normal text-[#4a5068] dark:text-[#b6b9d6]">— {{ $carreraFiltro }}</span>
                        @endif
                        <div class="mt-0.5 text-xs text-[#8890aa] dark:text-[#82859f]">{{ $examenTitulo }}</div>
                    </div>
                    <button wire:click="$set('modalOpen', false)" class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-lg border border-[#6366f1]/12 bg-[#f5f6fd] text-[#4a5068] transition-colors duration-200 hover:bg-[#6366f1]/10 hover:text-[#534ab7] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#b6b9d6] dark:hover:bg-[#8b83e8]/18 dark:hover:text-[#8b83e8]" aria-label="Cerrar">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>

                <ul class="m-0 flex max-h-[420px] list-none flex-col gap-0.5 overflow-y-auto p-0 [scrollbar-width:thin]">
                    @foreach($modalRanking as $i => $item)
                    <li class="flex items-center gap-2.5 rounded-[10px] px-2 py-2.5 transition-colors duration-150 {{ $item['es_yo'] ? 'bg-[#6366f1]/10 dark:bg-[#8b83e8]/18' : 'hover:bg-[#f5f6fd] dark:hover:bg-[#21233a]' }}">
                        <span class="w-7 shrink-0 text-center text-xs font-semibold text-[#8890aa] dark:text-[#82859f]">
                            @if($i === 0)<i class="ti ti-trophy text-[13px] text-[#b45309]"></i>
                            @elseif($i === 1)<i class="ti ti-medal text-[13px] text-[#6b7280]"></i>
                            @elseif($i === 2)<i class="ti ti-medal text-[13px] text-[#92400e]"></i>
                            @else #{{ $i + 1 }}
                            @endif
                        </span>
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-[11px] font-bold
                            {{ $item['es_yo']
                                ? 'border-[#6366f1]/30 bg-[#6366f1]/10 text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]'
                                : 'border-[#6366f1]/12 bg-[#f5f6fd] text-[#4a5068] dark:border-[#9482ff]/18 dark:bg-[#21233a] dark:text-[#b6b9d6]' }}">

                            {{ strtoupper(implode('', array_map(fn($w) => $w[0] ?? '',array_slice(array_filter(explode(' ', trim($item['nombre']))), 0, 2)))) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-[13px] font-medium {{ $item['es_yo'] ? 'font-semibold text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#1a1e35] dark:text-[#f1f2fb]' }}">
                                {{ $item['nombre'] }}
                                @if($item['es_yo'])<span class="ml-1 rounded bg-[#6366f1]/10 px-1.5 py-px text-[11px] font-medium text-[#534ab7] dark:bg-[#8b83e8]/18 dark:text-[#8b83e8]">(tú)</span>@endif
                            </div>
                            <div class="truncate text-[11px] {{ $item['es_yo'] ? 'text-[#534ab7]/75 dark:text-[#8b83e8]/75' : 'text-[#8890aa] dark:text-[#82859f]' }}">{{ $item['carrera'] }}</div>
                        </div>
                        <div class="shrink-0 text-[13px] font-semibold {{ $item['es_yo'] ? 'text-[#534ab7] dark:text-[#8b83e8]' : 'text-[#4a5068] dark:text-[#b6b9d6]' }}">
                            {{ number_format($item['puntaje']) }} pts
                        </div>
                    </li>
                    @endforeach
                </ul>

                @if($modalHasMore)
                <div class="shrink-0 border-t border-black/6 px-6 py-4 dark:border-white/8">
                    <button wire:click="cargarMasModal" class="w-full">
                        <span wire:loading.remove wire:target="cargarMasModal">
                            Cargar más <i class="ti ti-chevron-down"></i>
                        </span>
                        <span wire:loading wire:target="cargarMasModal">Cargando...</span>
                    </button>
                </div>
                @endif

            </div>
        </div>
        @endif

    </div>
</x-filament-panels::page>
