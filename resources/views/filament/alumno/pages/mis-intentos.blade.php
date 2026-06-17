@push('styles')
<link rel="stylesheet" href="{{ asset('css/examen-ordinario.css') }}">
<link rel="stylesheet" href="{{ asset('css/mis-intentos.css') }}">
@endpush
<x-filament-panels::page>
    <div class="eo-page">
        <div class="mi-layout">

            {{-- ══════════════════════════════════════════════════
                 PANEL IZQUIERDO — lista de exámenes
            ══════════════════════════════════════════════════ --}}
            <div class="eo-card mi-panel-examenes">

                <div class="eo-card-header">
                    <div class="eo-card-header-icon">
                        @svg('heroicon-o-academic-cap', 'w-5 h-5')
                    </div>
                    <div class="eo-card-header-text">
                        <div class="eo-card-header-title">Mis Exámenes</div>
                        <div class="eo-card-header-sub">{{ count($examenes) }} con intentos</div>
                    </div>
                </div>

                @if(empty($examenes))
                <div class="eo-empty">
                    @svg('heroicon-o-inbox', 'w-10 h-10')
                    <span>Aún no has rendido ningún examen.</span>
                </div>
                @else
                <ul class="mi-exam-list">
                    @foreach($examenes as $examen)
                    <li
                        wire:click="seleccionarExamen({{ $examen['id'] }})"
                        class="mi-exam-item {{ $examenId === $examen['id'] ? 'mi-exam-item-active' : '' }}">
                        <div class="mi-exam-icon">
                            @svg('heroicon-o-document-text', 'w-5 h-5')
                        </div>
                        <div class="mi-exam-info">
                            <div class="mi-exam-titulo">{{ $examen['titulo'] }}</div>
                            <div class="mi-exam-meta">
                                @svg('heroicon-o-arrow-path', 'w-3 h-3')
                                {{ $examen['intentos'] }} {{ $examen['intentos'] === 1 ? 'intento' : 'intentos' }}
                            </div>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 mi-exam-arrow')
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════════
                 PANEL DERECHO — intentos del examen seleccionado
            ══════════════════════════════════════════════════ --}}
            <div class="eo-card mi-panel-intentos">

                @if(!$examenId)
                {{-- Sin selección --}}
                <div class="mi-empty-state">
                    @svg('heroicon-o-cursor-arrow-rays', 'w-16 h-16')
                    <p>Selecciona un examen para<br>ver tu historial de intentos</p>
                </div>

                @else
                <div class="eo-card-header">
                    <div class="eo-card-header-icon">
                        @svg('heroicon-o-clock', 'w-5 h-5')
                    </div>
                    <div class="eo-card-header-text">
                        <div class="eo-card-header-title">Historial de Intentos</div>
                        <div class="eo-card-header-sub">{{ Str::limit($examenTitulo, 40) }}</div>
                    </div>
                </div>

                @if(empty($intentos))
                <div class="mi-empty-state">
                    @svg('heroicon-o-inbox', 'w-12 h-12')
                    <p>No hay intentos para este examen.</p>
                </div>
                @else
                <div class="mi-intentos-wrap">
                    @foreach($intentos as $idx => $intento)
                    <div class="mi-intento-card {{ $intento['aprobado'] ? 'mi-intento-aprobado' : 'mi-intento-fallido' }}">

                        {{-- Número de intento + badge --}}
                        <div class="mi-intento-header">
                            <span class="mi-intento-num">Intento #{{ count($intentos) - $idx }}</span>
                            @if($intento['aprobado'])
                            <span class="mi-badge mi-badge-aprobado">
                                @svg('heroicon-o-check-circle', 'w-3 h-3') Aprobado
                            </span>
                            @else
                            <span class="mi-badge mi-badge-fallido">
                                @svg('heroicon-o-x-circle', 'w-3 h-3') No aprobado
                            </span>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="mi-intento-stats">
                            <div class="mi-stat">
                                <div class="mi-stat-label">Puntaje</div>
                                <div class="mi-stat-value {{ $intento['aprobado'] ? 'mi-stat-green' : 'mi-stat-red' }}">
                                    {{ number_format($intento['puntaje'], 2) }}
                                </div>
                            </div>
                            <div class="mi-stat">
                                <div class="mi-stat-label">Carrera</div>
                                <div class="mi-stat-value mi-stat-small">{{ $intento['carrera'] }}</div>
                            </div>
                            <div class="mi-stat">
                                <div class="mi-stat-label">Tiempo</div>
                                <div class="mi-stat-value">
                                    {{ $intento['tiempo_min'] }}<span class="mi-stat-unit">m</span>
                                    {{ $intento['tiempo_seg'] }}<span class="mi-stat-unit">s</span>
                                </div>
                            </div>
                            <div class="mi-stat">
                                <div class="mi-stat-label">Fecha</div>
                                <div class="mi-stat-value mi-stat-small">{{ $intento['fecha'] }}</div>
                            </div>
                        </div>

                        {{-- Acción --}}
                        <div class="mi-acciones">
                            <button
                                wire:click="verDetalle({{ $intento['id'] }})"
                                class="mi-btn-detalle">
                                @svg('heroicon-o-magnifying-glass', 'w-4 h-4')
                                <span class="mi-btn-text">Ver detalle</span>
                            </button>

                            @if($intento['pdf_path'])
                            <a href="{{ asset('storage/' . $intento['pdf_path']) }}"
                                target="_blank"
                                download
                                class="mi-btn-detalle mi-btn-download"
                                x-data="{ descargando: false }"
                                @click="descargando = true; setTimeout(() => descargando = false, 1000)">

                                {{-- Estado Normal --}}
                                <span class="mi-btn-estado" x-show="!descargando">
                                    <!-- @svg('heroicon-o-arrow-down-tray', 'w-4 h-4') -->
                                    <span class="mi-btn-text">{{ basename($intento['pdf_path']) }}</span>
                                </span>

                                {{-- Estado Descargando --}}
                                <span class="mi-btn-estado" x-show="descargando" x-cloak>
                                    <!-- @svg('heroicon-o-arrow-path', 'w-4 h-4 animate-spin') -->
                                    <span class="mi-btn-text">Descargando...</span>
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
        <div class="eo-modal-backdrop" wire:click.self="cerrarModal">
            <div class="eo-modal mi-modal">

                <div class="eo-modal-header mi-modal-header-custom">
                    <div class="mi-modal-header-content">

                        {{-- COLUMNA IZQUIERDA: Textos y Puntos --}}
                        <div class="mi-modal-info-left">
                            <span class="eo-modal-title">Detalle del Intento</span>
                            <div class="eo-modal-exam-name">{{ $modalExamenTitulo }}</div>
                            <div class="mi-modal-resumen">
                                <span class="mi-modal-puntaje">{{ number_format($modalPuntaje, 2) }} pts</span>
                                @if($modalAprobado)
                                <span class="mi-badge mi-badge-aprobado">
                                    @svg('heroicon-o-check-circle', 'w-3 h-3') Aprobado
                                </span>
                                @else
                                <span class="mi-badge mi-badge-fallido">
                                    @svg('heroicon-o-x-circle', 'w-3 h-3') No aprobado
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA: Barra de progreso --}}
                        <div class="mi-modal-info-right">
                            @php
                            $porcentaje = $modalPuntajeMinimo > 0
                            ? min(100, round(($modalPuntaje / $modalPuntajeMinimo) * 100, 1))
                            : 0;
                            @endphp
                            <div class="mi-progress-wrap">
                                <div class="mi-progress-labels">
                                    <span class="mi-progress-label-left">
                                        Área: <strong>{{ $modalAreaNombre }}</strong>
                                    </span>
                                    <span class="mi-progress-label-right">
                                        Mínimo: <strong>{{ number_format($modalPuntajeMinimo, 2) }} pts</strong>
                                    </span>
                                </div>
                                <div class="mi-progress-bar-bg">
                                    <div
                                        class="mi-progress-bar-fill {{ $modalAprobado ? 'mi-progress-green' : 'mi-progress-red' }}"
                                        style="width: {{ $porcentaje }}%">
                                    </div>
                                </div>
                                <div class="mi-progress-pct">{{ $porcentaje }}% del mínimo requerido</div>
                            </div>
                        </div>

                    </div>

                    <button wire:click="cerrarModal" class="eo-modal-close">
                        @svg('heroicon-o-x-mark', 'w-5 h-5')
                    </button>
                </div>

                {{-- Leyenda --}}
                <div class="mi-leyenda">
                    <span class="mi-leyenda-item">
                        <span class="mi-dot mi-dot-correcta"></span> Correcta
                    </span>
                    <span class="mi-leyenda-item">
                        <span class="mi-dot mi-dot-incorrecta"></span> Incorrecta
                    </span>
                </div>

                {{-- Tabla de respuestas --}}
                <div class="eo-rank-list-scroll mi-detalle-scroll">
                    <table class="mi-detalle-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Asignatura</th>
                                <th>Tu Resp.</th>
                                <th>Correcta</th>
                                <th>Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modalDetalle as $fila)
                            <tr class="{{ $fila['es_correcta'] ? 'mi-fila-correcta' : 'mi-fila-incorrecta' }}">
                                <td class="mi-td-num">{{ str_pad($fila['numero'], 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="mi-td-asig">{{ ucfirst(str_replace('_', ' ', $fila['asignatura'])) }}</td>
                                <td class="mi-td-resp">
                                    <span class="mi-opcion {{ $fila['es_correcta'] ? 'mi-opcion-ok' : 'mi-opcion-fail' }}">
                                        {{ $fila['marcada'] }}
                                    </span>
                                </td>
                                <td class="mi-td-resp">
                                    <span class="mi-opcion mi-opcion-correcta">
                                        {{ $fila['correcta'] }}
                                    </span>
                                </td>
                                <td class="mi-td-pts {{ $fila['puntos'] > 0 ? 'mi-pts-pos' : 'mi-pts-neg' }}">
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
</x-filament-panels::page>