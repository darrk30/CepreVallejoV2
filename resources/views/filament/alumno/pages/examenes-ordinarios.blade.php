@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
@endpush
<x-filament-panels::page>

    <div class="eo-page">
        <div class="eo-layout">

            {{-- ══════════════════════════════════════════════════════
                 PANEL IZQUIERDO — quest log de exámenes
            ══════════════════════════════════════════════════════ --}}
            <div class="eo-card">

                <div class="eo-card-header">
                    <div class="eo-card-header-icon">
                        @svg('heroicon-o-academic-cap', 'w-5 h-5')
                    </div>
                    <div class="eo-card-header-text">
                        <div class="eo-card-header-title">Exámenes disponibles</div>
                        <div class="eo-card-header-sub">{{ count($examenes) }} exámenes activos</div>
                    </div>
                </div>

                <div class="eo-search-wrap">
                    <i class="ti ti-search"></i>
                    <input
                        type="text"
                        wire:model.live="examSearch"
                        placeholder="Buscar misión..."
                        class="eo-input" />
                </div>

                <div class="eo-exam-list">
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
                    <div class="eo-exam-row {{ $examenId === $examen['id'] ? 'eo-exam-row-active' : '' }}">

                        <div class="eo-exam-icon">
                            @svg('heroicon-o-document-text', 'w-5 h-5')
                        </div>

                        <div class="eo-exam-info" wire:click="seleccionarExamen({{ $examen['id'] }})" style="cursor:pointer;">
                            <div class="eo-exam-title">{{ $examen['titulo'] }}</div>
                            <div class="eo-exam-meta">
                                <span class="eo-pill eo-pill-violet">
                                    @svg('heroicon-o-clock', 'eo-icon-meta') {{ $examen['duracion'] }} min
                                </span>

                                <span class="eo-dot">·</span>

                                <span class="eo-meta-item">
                                    @svg('heroicon-o-list-bullet', 'eo-icon-meta') {{ $examen['preguntas'] }} preg.
                                </span>

                                <span class="eo-dot">·</span>

                                <span class="eo-meta-item">
                                    @svg('heroicon-o-calendar', 'eo-icon-meta') {{ $examen['fecha'] }}
                                </span>
                            </div>
                        </div>

                        <div class="eo-exam-actions">
                            <!-- Botón Secundario: Ver Intentos -->
                            <!-- Nota: Usamos 'title' nativo para el tooltip, o puedes usar x-tooltip="'Ver mis intentos'" si usas Filament/Alpine -->
                            <button class="eo-btn-action eo-btn-secondary"
                                title="Ver historial de intentos"
                                wire:click="irAIntentos({{ $examen['id'] }})">
                                @svg('heroicon-o-clipboard-document-list', 'w-5 h-5')
                                <span class="eo-btn-text">Intentos</span>
                            </button>

                            <!-- Botón Primario: Iniciar Examen -->
                            <a href="{{ \App\Filament\Alumno\Pages\RendirExamen::getUrl(['examen_id' => $examen['id']]) }}"
                                class="eo-btn-action eo-btn-primary"
                                title="Iniciar Misión / Rendir Examen">
                                @svg('heroicon-o-rocket-launch', 'w-5 h-5')
                                <span class="eo-btn-text">Iniciar</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="eo-empty">
                        <i class="ti ti-ghost"></i>
                        <span>No se encontraron misiones.</span>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════
                 PANEL DERECHO — leaderboard
            ══════════════════════════════════════════════════════ --}}
            <div class="eo-card">

                <div class="eo-card-header">
                    <div class="eo-card-header-icon">
                        @svg('heroicon-o-trophy', 'w-5 h-5')
                    </div>
                    <div class="eo-card-header-text">
                        <div class="eo-card-header-title">Tabla de clasificación</div>
                        @if($examenTitulo)
                        <div class="eo-card-header-sub">{{ Str::limit($examenTitulo, 32) }}</div>
                        @else
                        <div class="eo-card-header-sub">Selecciona un examen</div>
                        @endif
                    </div>
                </div>

                {{-- Filtro carrera --}}
                @if($examenId && !empty($carreras))
                <div class="eo-rank-filter">
                    <select wire:model.live="carreraFiltro"
                        wire:change="filtrarCarrera($event.target.value)"
                        class="eo-select">
                        <option value="">Todas las carreras</option>
                        @foreach($carreras as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Sin examen --}}
                @if(! $examenId)
                <div class="eo-rank-empty">
                    <i class="ti ti-tournament"></i>
                    <p>Elige un examen para<br>ver el ranking de los alumnos</p>
                </div>

                {{-- Sin intentos --}}
                @elseif(empty($ranking))
                <div class="eo-rank-empty">
                    <i class="ti ti-mood-empty"></i>
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
                <div class="eo-podio">

    {{-- Plata (#2) --}}
    @if($top2)
    <div class="eo-podio-slot eo-podio-slot-2">
        <div class="eo-podio-avatar">
            <div class="eo-podio-circle eo-podio-circle-2 {{ $top2['es_yo'] ? 'eo-avatar-me-podio' : '' }}">
                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top2['nombre']), 0, 2)))) }}
            </div>
            @if($top2['es_yo'])
                <div class="eo-podio-me-badge">TÚ</div>
            @endif
        </div>
        <div class="eo-podio-name {{ $top2['es_yo'] ? 'eo-rank-name-me' : '' }}" title="{{ $top2['nombre'] }}">
            {{ $top2['nombre'] }}
        </div>
        <div class="eo-podio-pts">{{ number_format($top2['puntaje']) }}</div>
        <div class="eo-podio-base eo-podio-base-2">#2</div>
    </div>
    @endif

    {{-- Oro (#1) --}}
    @if($top1)
    <div class="eo-podio-slot eo-podio-slot-1">
        <div class="eo-podio-avatar">
            <div class="eo-podio-crown">👑</div>
            <div class="eo-podio-circle eo-podio-circle-1 {{ $top1['es_yo'] ? 'eo-avatar-me-podio' : '' }}">
                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top1['nombre']), 0, 2)))) }}
            </div>
            @if($top1['es_yo'])
                <div class="eo-podio-me-badge">TÚ</div>
            @endif
        </div>
        <div class="eo-podio-name {{ $top1['es_yo'] ? 'eo-rank-name-me' : '' }}" title="{{ $top1['nombre'] }}">
            {{ $top1['nombre'] }}
        </div>
        <div class="eo-podio-pts">{{ number_format($top1['puntaje']) }}</div>
        <div class="eo-podio-base eo-podio-base-1">#1</div>
    </div>
    @endif

    {{-- Bronce (#3) --}}
    @if($top3)
    <div class="eo-podio-slot eo-podio-slot-3">
        <div class="eo-podio-avatar">
            <div class="eo-podio-circle eo-podio-circle-3 {{ $top3['es_yo'] ? 'eo-avatar-me-podio' : '' }}">
                {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $top3['nombre']), 0, 2)))) }}
            </div>
            @if($top3['es_yo'])
                <div class="eo-podio-me-badge">TÚ</div>
            @endif
        </div>
        <div class="eo-podio-name {{ $top3['es_yo'] ? 'eo-rank-name-me' : '' }}" title="{{ $top3['nombre'] }}">
            {{ $top3['nombre'] }}
        </div>
        <div class="eo-podio-pts">{{ number_format($top3['puntaje']) }}</div>
        <div class="eo-podio-base eo-podio-base-3">#3</div>
    </div>
    @endif

</div>

                {{-- Mi posición si NO estoy en top 3 --}}
                @php $myItem = collect($ranking)->firstWhere('es_yo', true); @endphp
                @if($myItem && $myPos > 3)
                <div class="eo-my-rank-banner">
                    <span>Tu posición</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span>#{{ $myPos }}</span>
                        <strong>{{ number_format($myItem['puntaje']) }} pts</strong>
                    </div>
                </div>
                @endif

                {{-- Lista 4 en adelante --}}
                @if(!empty($rest))
                <ul class="eo-rank-list">
                    @foreach($rest as $i => $item)
                    @php $pos = $i + 4; @endphp
                    <li class="eo-rank-item {{ $item['es_yo'] ? 'eo-rank-me' : '' }}">
                        <span class="eo-rank-pos">#{{ $pos }}</span>
                        <div class="eo-avatar {{ $item['es_yo'] ? 'eo-avatar-me' : '' }}">
                            {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $item['nombre']), 0, 2)))) }}
                        </div>
                        <div class="eo-rank-info">
                            <div class="eo-rank-name {{ $item['es_yo'] ? 'eo-rank-name-me' : '' }}">
                                {{ $item['nombre'] }}
                                @if($item['es_yo'])<span class="eo-you-badge">(tú)</span>@endif
                            </div>
                            <div class="eo-rank-carrera {{ $item['es_yo'] ? 'eo-rank-carrera-me' : '' }}">{{ $item['carrera'] }}</div>
                        </div>
                        <div class="eo-rank-pts {{ $item['es_yo'] ? 'eo-rank-pts-me' : '' }}">
                            {{ number_format($item['puntaje']) }} pts
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif

                <div class="eo-rank-footer">
                    <button wire:click="abrirModal" class="eo-btn-action eo-btn-secondary">
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
        <div class="eo-modal-backdrop" wire:click.self="$set('modalOpen', false)">
            <div class="eo-modal">

                <div class="eo-modal-header">
                    <div>
                        <span class="eo-modal-title">Ranking completo</span>
                        @if($carreraFiltro)
                        <span class="eo-modal-subtitle">— {{ $carreraFiltro }}</span>
                        @endif
                        <div class="eo-modal-exam-name">{{ $examenTitulo }}</div>
                    </div>
                    <button wire:click="$set('modalOpen', false)" class="eo-modal-close" aria-label="Cerrar">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>

                <ul class="eo-rank-list eo-rank-list-scroll">
                    @foreach($modalRanking as $i => $item)
                    <li class="eo-rank-item {{ $item['es_yo'] ? 'eo-rank-me' : '' }}">
                        <span class="eo-rank-pos">
                            @if($i === 0)<i class="ti ti-trophy" style="color:var(--eo-gold);font-size:13px;"></i>
                            @elseif($i === 1)<i class="ti ti-medal" style="color:var(--eo-silver);font-size:13px;"></i>
                            @elseif($i === 2)<i class="ti ti-medal" style="color:var(--eo-bronze);font-size:13px;"></i>
                            @else #{{ $i + 1 }}
                            @endif
                        </span>
                        <div class="eo-avatar {{ $item['es_yo'] ? 'eo-avatar-me' : '' }}">
                            {{ strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $item['nombre']), 0, 2)))) }}
                        </div>
                        <div class="eo-rank-info">
                            <div class="eo-rank-name {{ $item['es_yo'] ? 'eo-rank-name-me' : '' }}">
                                {{ $item['nombre'] }}
                                @if($item['es_yo'])<span class="eo-you-badge">(tú)</span>@endif
                            </div>
                            <div class="eo-rank-carrera {{ $item['es_yo'] ? 'eo-rank-carrera-me' : '' }}">{{ $item['carrera'] }}</div>
                        </div>
                        <div class="eo-rank-pts {{ $item['es_yo'] ? 'eo-rank-pts-me' : '' }}">
                            {{ number_format($item['puntaje']) }} pts
                        </div>
                    </li>
                    @endforeach
                </ul>

                @if($modalHasMore)
                <div class="eo-modal-footer">
                    <button wire:click="cargarMasModal" class="eo-btn-full">
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