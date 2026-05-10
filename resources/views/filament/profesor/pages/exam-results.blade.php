@push('styles')
    <link rel="stylesheet" href="{{ asset('css/exam-results.css') }}">
@endpush
<x-filament-panels::page>
    @php
        $exam     = $this->exam;
        $attempts = $this->attempts;
        $stats    = $this->stats;
        $selected = $this->selectedAttempt;
        $totalPts = $exam->questions->sum('puntos');
    @endphp

    <div class="er-root">

        {{-- Back --}}
        @php
            $courseSlug = $exam->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->slug;
        @endphp
        @if ($courseSlug)
            <a href="{{ \App\Filament\Profesor\Pages\ManageCourseContent::getUrl(['courseSlug' => $courseSlug]) }}"
               class="er-back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver al curso
            </a>
        @endif

        {{-- Banner --}}
        <div class="er-banner">
            <div class="er-banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                </svg>
            </div>
            <div class="er-banner-info">
                <p class="er-banner-title">{{ $exam->titulo }}</p>
                <div class="er-banner-meta">
                    <span class="er-pill er-pill-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12"/>
                        </svg>
                        {{ $exam->questions->count() }} preguntas
                    </span>
                    <span class="er-pill er-pill-amber">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                        </svg>
                        Aprobar con {{ $exam->puntaje_minimo }} / {{ $totalPts }} pts
                    </span>
                    <span class="er-pill er-pill-green">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                        {{ $stats['total'] }} {{ $stats['total'] === 1 ? 'estudiante' : 'estudiantes' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="er-stats">
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--text)">{{ $stats['total'] }}</div>
                <div class="er-stat-label">Finalizaron</div>
            </div>
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--green)">{{ $stats['aprobados'] }}</div>
                <div class="er-stat-label">Aprobados</div>
            </div>
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--red)">{{ $stats['desaprobados'] }}</div>
                <div class="er-stat-label">Desaprobados</div>
            </div>
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--brand)">{{ $stats['promedio'] }}</div>
                <div class="er-stat-label">Promedio pts</div>
            </div>
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--green)">{{ $stats['max'] }}</div>
                <div class="er-stat-label">Puntaje máx</div>
            </div>
            <div class="er-stat">
                <div class="er-stat-val" style="color:var(--amber)">{{ $stats['min'] }}</div>
                <div class="er-stat-label">Puntaje mín</div>
            </div>
        </div>

        {{-- Layout: lista + panel --}}
        <div class="er-layout {{ $selected ? 'with-panel' : '' }}" id="er-layout">

            {{-- Lista de estudiantes --}}
            <div class="er-list-card">
                <div class="er-list-head">
                    <span class="er-list-head-title">Estudiantes</span>
                    <label class="er-search">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z"/>
                        </svg>
                        <input type="text" placeholder="Buscar estudiante…" id="er-search-input"
                               oninput="filterStudents(this.value)">
                    </label>
                </div>

                @if ($attempts->isEmpty())
                    <div class="er-empty">
                        <div class="er-empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                        </div>
                        <p class="er-empty-title">Sin resultados aún</p>
                        <p class="er-empty-sub">Ningún estudiante ha finalizado este examen todavía.</p>
                    </div>
                @else
                    <div id="er-students-list">
                        @foreach ($attempts as $i => $attempt)
                            @php
                                $medals = ['🥇','🥈','🥉'];
                                $medal  = $medals[$i] ?? null;
                            @endphp
                            <div class="er-student-row {{ $selectedAttemptId === $attempt['id'] ? 'active' : '' }}"
                                 data-name="{{ strtolower($attempt['student_name']) }} {{ strtolower($attempt['student_email']) }}"
                                 wire:click="selectAttempt({{ $attempt['id'] }})"
                                 id="er-row-{{ $attempt['id'] }}">

                                @if ($medal)
                                    <span class="er-medal">{{ $medal }}</span>
                                @else
                                    <span class="er-rank">#{{ $i + 1 }}</span>
                                @endif

                                <div class="er-avatar {{ $attempt['aprobado'] ? 'er-avatar-approved' : 'er-avatar-failed' }}">
                                    {{ $attempt['student_avatar'] }}
                                </div>

                                <div class="er-student-info">
                                    <div class="er-student-name">{{ $attempt['student_name'] }}</div>
                                    <div class="er-student-email">{{ $attempt['student_email'] }}</div>
                                </div>

                                <div class="er-student-right">
                                    <span class="er-student-score {{ $attempt['aprobado'] ? 'approved' : 'failed' }}">
                                        {{ number_format($attempt['puntaje'], 1) }}
                                        <span style="font-size:.65rem;opacity:.7;">/ {{ $totalPts }}</span>
                                    </span>
                                    @if ($attempt['duracion'])
                                        <span class="er-student-time">⏱ {{ $attempt['duracion'] }}</span>
                                    @endif
                                    <span class="er-verdict-pill {{ $attempt['aprobado'] ? 'approved' : 'failed' }}">
                                        {{ $attempt['aprobado'] ? 'Aprobado' : 'Desaprobado' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Panel de detalle --}}
            @if ($selected)
                <div class="er-panel">

                    {{-- Head --}}
                    <div class="er-panel-head">
                        <div class="er-panel-student">
                            <div class="er-panel-name">{{ $selected['student_name'] }}</div>
                            <div class="er-panel-pills">
                                <span class="er-pill {{ $selected['aprobado'] ? 'er-pill-green' : '' }}"
                                      style="{{ !$selected['aprobado'] ? 'background:var(--red-soft);color:var(--red);border-color:var(--red-border);' : '' }}">
                                    {{ $selected['aprobado'] ? '✓ Aprobado' : '✗ Desaprobado' }}
                                </span>
                                @if ($selected['duracion'])
                                    <span class="er-pill er-pill-blue">⏱ {{ $selected['duracion'] }}</span>
                                @endif
                                @if ($selected['fecha_fin'])
                                    <span class="er-pill er-pill-amber">{{ $selected['fecha_fin'] }}</span>
                                @endif
                            </div>
                        </div>
                        <button class="er-close-btn" wire:click="clearSelection" title="Cerrar panel">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Mini stats --}}
                    <div class="er-score-bar">
                        @php
                            $correctas   = collect($selected['detalle'])->where('es_correcta', true)->count();
                            $incorrectas = collect($selected['detalle'])->where('es_correcta', false)->where('sin_respuesta', false)->count();
                            $sinResp     = collect($selected['detalle'])->where('sin_respuesta', true)->count();
                        @endphp
                        <div class="er-score-mini">
                            <div class="er-score-mini-val" style="color:var(--green)">{{ $correctas }}</div>
                            <div class="er-score-mini-label">Correctas</div>
                        </div>
                        <div class="er-score-mini">
                            <div class="er-score-mini-val" style="color:var(--red)">{{ $incorrectas }}</div>
                            <div class="er-score-mini-label">Incorrectas</div>
                        </div>
                        <div class="er-score-mini">
                            <div class="er-score-mini-val" style="color:var(--text-3)">{{ $sinResp }}</div>
                            <div class="er-score-mini-label">Sin responder</div>
                        </div>
                    </div>

                    {{-- Detalle de preguntas --}}
                    <div class="er-panel-body">
                        @foreach ($selected['detalle'] as $qd)
                            @php
                                $cardClass  = $qd['sin_respuesta'] ? 'q-unanswered' : ($qd['es_correcta'] ? 'q-correct' : 'q-wrong');
                                $badgeClass = $qd['sin_respuesta'] ? 'unanswered' : ($qd['es_correcta'] ? 'correct' : 'wrong');
                                $badgeText  = $qd['sin_respuesta'] ? 'Sin responder' : ($qd['es_correcta'] ? 'Correcto' : 'Incorrecto');
                            @endphp
                            <div class="er-q-card {{ $cardClass }}">
                                <div class="er-q-head">
                                    <span class="er-q-num">P{{ $qd['numero'] }}</span>
                                    <span class="er-q-pts">{{ $qd['puntos'] }} pt{{ $qd['puntos'] != 1 ? 's' : '' }}</span>
                                    <span class="er-q-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>
                                <div class="er-q-text">{{ $qd['pregunta'] }}</div>
                                <div class="er-q-options">
                                    @foreach ($qd['options'] as $oi => $opt)
                                        @php
                                            $isCorrecta   = $qd['correcta'] && $opt->id === $qd['correcta']->id;
                                            $isRespondida = $qd['respondida'] && $opt->id === $qd['respondida']->id;
                                            $optClass     = '';
                                            if ($isCorrecta)            $optClass = 'opt-correct';
                                            elseif ($isRespondida)      $optClass = 'opt-wrong';
                                        @endphp
                                        <div class="er-opt {{ $optClass }}">
                                            <div class="er-opt-letter">{{ chr(65 + $oi) }}</div>
                                            <div style="flex:1;min-width:0;">
                                                <div>{{ $opt->texto_opcion }}</div>
                                                @if ($isCorrecta && $isRespondida)
                                                    <div class="er-opt-hint hint-correct">✓ Respuesta del alumno · Correcta</div>
                                                @elseif ($isCorrecta)
                                                    <div class="er-opt-hint hint-correct">✓ Respuesta correcta</div>
                                                @elseif ($isRespondida)
                                                    <div class="er-opt-hint hint-wrong">✗ El alumno eligió esta</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @else
                {{-- Placeholder cuando no hay selección --}}
                <div class="er-panel" style="{{ $attempts->isEmpty() ? 'display:none;' : '' }}">
                    <div class="er-panel-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                        <p>Selecciona un estudiante para ver sus respuestas detalladas</p>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <script>
        function filterStudents(query) {
            const q = query.toLowerCase().trim();
            document.querySelectorAll('#er-students-list .er-student-row').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = (!q || name.includes(q)) ? '' : 'none';
            });
        }
    </script>

</x-filament-panels::page>