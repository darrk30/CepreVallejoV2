@push('styles')
    <link rel="stylesheet" href="{{ asset('css/take-exam.css') }}">
@endpush
<x-filament-panels::page>
    @php
        $prevAnswers = $this->attempt?->respuestas_enviadas ?? [];
        $prevAnswersJson = json_encode((object) $prevAnswers);
        $isResume = !$this->showResults && count($prevAnswers) > 0;
        $isPreview = $this->isPreview;
        $showResults = $this->showResults;
        $remaining = $isPreview || $showResults ? $this->exam->duracion_minutos * 60 : $this->getRemainingSeconds();
        $displayM = str_pad(intdiv($remaining, 60), 2, '0', STR_PAD_LEFT);
        $displayS = str_pad($remaining % 60, 2, '0', STR_PAD_LEFT);
        $intentosRestantes = $isPreview ? 0 : $this->intentos_restantes;
        $intentosMaximos = $this->exam->intentos_maximos;

        $resultsData = $showResults ? $this->results_data : [];
        $totalPregs = $this->exam->questions->count();
        $totalPuntos = $this->exam->questions->sum('puntos');
        $puntajeMin = $this->exam->puntaje_minimo;
        $puntajeObt = $this->attempt?->puntaje_obtenido ?? 0;
        $aprobado = $puntajeObt >= $puntajeMin;
        $aciertos = $showResults ? collect($resultsData)->where('es_correcta', true)->count() : 0;
        $errores = $showResults
            ? collect($resultsData)->where('es_correcta', false)->where('sin_respuesta', false)->count()
            : 0;
        $sinResp = $showResults ? collect($resultsData)->where('sin_respuesta', true)->count() : 0;
    @endphp

    {{-- ── Overlay Procesando ── --}}
    <div class="processing-overlay" id="processing-overlay">
        <div class="processing-card">
            <div class="processing-spinner"></div>
            <p class="processing-title">Procesando examen…</p>
            <p class="processing-sub">Por favor espera, estamos calculando tu resultado.</p>
        </div>
    </div>

    <div class="exam-root">

        @if ($isPreview)
            <div class="preview-banner">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" style="width:16px;height:16px;flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Estás viendo este examen en <strong>modo preview</strong> — eres el creador o profesor asignado. No se
                registrará ningún intento.
            </div>
        @endif

        {{-- Badges ── --}}
        <div class="exam-meta-bar">
            <span class="exam-badge badge-blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" style="width:11px;height:11px">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                {{ $this->exam->questions->count() }} preguntas
            </span>
            <span class="exam-badge badge-amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" style="width:11px;height:11px">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
                {{ $this->exam->questions->sum('puntos') }} puntos
            </span>
            <span class="exam-badge badge-green">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" style="width:11px;height:11px">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Aprobar con {{ $this->exam->puntaje_minimo }} pts
            </span>
            @if (!$isPreview && !$showResults && $intentosMaximos > 1)
                <span class="exam-badge badge-red">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" style="width:11px;height:11px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    {{ $intentosRestantes }} intento{{ $intentosRestantes != 1 ? 's' : '' }}
                    restante{{ $intentosRestantes != 1 ? 's' : '' }}
                </span>
            @endif
            @if ($showResults)
                <span class="exam-badge {{ $aprobado ? 'badge-green' : 'badge-red' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" style="width:11px;height:11px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                    </svg>
                    Resultado
                </span>
            @endif
            @if ($isResume && !$isPreview)
                <span class="exam-badge badge-amber">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" style="width:11px;height:11px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.374-12.75a2.25 2.25 0 0 1 3.9 0l7.33 12.75Z" />
                    </svg>
                    Retomando intento
                </span>
            @endif
            @if ($isPreview)
                <span class="exam-badge badge-amber">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" style="width:11px;height:11px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Modo preview
                </span>
            @endif
        </div>

        <div class="exam-body">

            {{-- ── Sidebar ── --}}
            <div class="exam-sidebar">
                <div class="sidebar-head">
                    <p class="sidebar-exam-title">{{ $this->exam->titulo }}</p>
                    <p class="sidebar-exam-sub">
                        {{ $this->exam->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->nombre ?? '' }}
                        @if ($this->exam->detail?->titulo)
                            &middot; {{ $this->exam->detail->titulo }}
                        @endif
                    </p>
                </div>

                {{-- Timer --}}
                <div class="sidebar-timer" id="sidebar-timer-block" style="{{ $showResults ? 'display:none;' : '' }}">
                    <span class="sidebar-timer-label">{{ $isPreview ? 'Duración' : 'Tiempo restante' }}</span>
                    <span class="sidebar-timer-val" id="exam-timer">{{ $displayM }}:{{ $displayS }}</span>
                </div>

                @if (!$isPreview && !$showResults)
                    <div id="offline-badge" class="offline-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" style="width:11px;height:11px">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M6.111 6.89A6.992 6.992 0 003 12c0 1.657.575 3.176 1.527 4.368m4.167-8.586A5 5 0 0117 12c0 .74-.16 1.44-.448 2.073M9.764 9.766A3 3 0 0115 12m-3 3h.01" />
                        </svg>
                        Sin conexión — tiempo guardado localmente
                    </div>
                @endif

                {{-- Puntaje (visible en resultados) --}}
                <div class="sidebar-score-block {{ $showResults ? 'visible' : '' }}" id="sidebar-score-block">
                    <span class="sidebar-timer-label">Puntaje obtenido</span>
                    <span class="sidebar-timer-val" id="sidebar-score-val"
                        style="font-size:2rem;">{{ $puntajeObt }}</span>
                    <span style="font-size:0.65rem;color:var(--text-3);">de {{ $totalPuntos }} pts totales</span>
                </div>

                <div class="sidebar-prog">
                    <div class="sidebar-prog-top">
                        <span id="prog-label">{{ $showResults ? 'Correctas' : 'Progreso' }}</span>
                        <span id="prog-text">
                            @if ($showResults)
                                {{ $aciertos }} / {{ $totalPregs }}
                            @else
                                0 / {{ $totalPregs }}
                            @endif
                        </span>
                    </div>
                    <div class="sidebar-prog-track">
                        <div class="sidebar-prog-fill" id="prog-fill"
                            style="width:{{ $showResults && $totalPregs > 0 ? round(($aciertos / $totalPregs) * 100) : 0 }}%;
                                   background:{{ $showResults ? ($aprobado ? 'var(--green)' : 'var(--red)') : 'var(--brand)' }}">
                        </div>
                    </div>
                </div>

                <div class="sidebar-body">
                    <p class="sidebar-label">Preguntas</p>
                    <div class="exam-nav" id="exam-nav">
                        @foreach ($this->exam->questions as $qi => $q)
                            @php
                                $dotClass = '';
                                if ($showResults) {
                                    $rd = collect($resultsData)->firstWhere('question.id', $q->id);
                                    $dotClass = $rd
                                        ? ($rd['sin_respuesta']
                                            ? 'res-unanswered'
                                            : ($rd['es_correcta']
                                                ? 'res-correct'
                                                : 'res-wrong'))
                                        : 'res-unanswered';
                                }
                            @endphp
                            <div class="exam-nav-dot {{ $dotClass }}" id="dot-{{ $q->id }}"
                                title="Pregunta {{ $qi + 1 }}"
                                onclick="document.getElementById('q-{{ $q->id }}')?.scrollIntoView({behavior:'smooth',block:'start'})">
                                {{ $qi + 1 }}
                            </div>
                        @endforeach
                    </div>
                    <hr class="sidebar-divider">

                    {{-- Stats durante examen --}}
                    <div class="sidebar-stats-exam {{ $showResults ? 'hidden' : '' }}" id="sidebar-stats-exam">
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Puntaje</p>
                            <div class="sidebar-stat-card-val">{{ $this->exam->questions->sum('puntos') }}</div>
                            <div class="sidebar-stat-card-sub">{{ $this->exam->puntaje_minimo }} para aprobar</div>
                        </div>
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Duración</p>
                            <div class="sidebar-stat-card-val">{{ $this->exam->duracion_minutos }}<span
                                    style="font-size:.75rem;font-weight:500;color:var(--text-3);margin-left:2px;">min</span>
                            </div>
                            <div class="sidebar-stat-card-sub">{{ $this->exam->questions->count() }} preguntas</div>
                        </div>
                    </div>

                    {{-- Stats en resultados --}}
                    <div class="sidebar-stats-results {{ $showResults ? 'visible' : '' }}"
                        id="sidebar-stats-results">
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Correctas</p>
                            <div class="sidebar-stat-card-val" id="res-correctas-val" style="color:var(--green)">
                                {{ $aciertos }}</div>
                            <div class="sidebar-stat-card-sub">de {{ $totalPregs }}</div>
                        </div>
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Incorrectas</p>
                            <div class="sidebar-stat-card-val" id="res-incorrectas-val" style="color:var(--red)">
                                {{ $errores }}</div>
                            <div class="sidebar-stat-card-sub" id="res-sinresp-sub">{{ $sinResp }} sin responder
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Contenido principal ── --}}
            <div>

                {{--
                    BLOQUE EXAMEN
                    ─────────────────────────────────────────────────────────────
                    IMPORTANTE: Se eliminó wire:ignore para que Livewire pueda
                    aplicar la clase "hidden" al re-renderizar tras submitExam.
                    El JS también lo oculta via style inline como capa extra.
                    ─────────────────────────────────────────────────────────────
                --}}
                <div class="exam-questions-block {{ $showResults ? 'hidden' : '' }}" id="exam-questions-block">
                    @if (!$showResults)
                        @if ($isResume && !$isPreview)
                            <div class="resume-alert">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor"
                                    style="width:16px;height:16px;flex-shrink:0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                Estás retomando un intento en progreso. Tus respuestas anteriores han sido recuperadas.
                            </div>
                        @endif

                        <div class="questions-list">
                            @foreach ($this->exam->questions as $qi => $question)
                                <div class="q-card" id="q-{{ $question->id }}" data-qid="{{ $question->id }}">
                                    <div class="q-header">
                                        <span class="q-num-pill">Pregunta {{ $qi + 1 }}</span>
                                        <span class="q-pts">{{ $question->puntos }}
                                            pt{{ $question->puntos != 1 ? 's' : '' }}</span>
                                        @if (!$isPreview)
                                            <button class="q-clear-btn" id="clear-{{ $question->id }}"
                                                onclick="clearAnswer({{ $question->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    style="width:11px;height:11px">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                                Limpiar
                                            </button>
                                        @endif
                                    </div>
                                    <div class="q-text">{{ $question->texto_pregunta }}</div>
                                    @if ($question->imagen_path)
                                        <img src="{{ Storage::url($question->imagen_path) }}"
                                            alt="Imagen pregunta {{ $qi + 1 }}" class="q-img" loading="lazy">
                                    @endif
                                    <div
                                        class="options {{ $question->options->whereNotNull('imagen_path')->count() > 0 ? 'single-col' : '' }}">
                                        @foreach ($question->options as $option)
                                            <button class="opt" id="opt-{{ $option->id }}"
                                                data-qid="{{ $question->id }}" data-oid="{{ $option->id }}"
                                                onclick="{{ $isPreview ? '' : "selectOption({$question->id}, {$option->id}, this)" }}"
                                                @if ($isPreview) style="cursor:default;" @endif>
                                                <div class="opt-letter">{{ chr(64 + $loop->iteration) }}</div>
                                                <div class="opt-body">
                                                    <div class="opt-text">{{ $option->texto_opcion }}</div>
                                                    @if ($option->imagen_path)
                                                        <img src="{{ Storage::url($option->imagen_path) }}"
                                                            alt="Opción {{ chr(64 + $loop->iteration) }}"
                                                            class="opt-img" loading="lazy">
                                                    @endif
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Bloque RESULTADOS (Livewire lo renderiza, sin wire:ignore) --}}
                <div class="results-block {{ $showResults ? 'visible' : '' }}" id="results-block">
                    @if ($showResults)
                        <div class="results-hero">
                            <div class="results-hero-score {{ $aprobado ? 'approved' : 'failed' }}">
                                {{ $puntajeObt }}</div>
                            <div class="results-hero-label">puntos obtenidos de {{ $totalPuntos }}</div>
                            <div class="results-hero-verdict {{ $aprobado ? 'approved' : 'failed' }}">
                                @if ($aprobado)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" style="width:15px;height:15px">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    ¡Aprobado!
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" style="width:15px;height:15px">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.374-12.75a2.25 2.25 0 0 1 3.9 0l7.33 12.75Z" />
                                    </svg>
                                    Desaprobado
                                @endif
                            </div>
                            <div class="results-stats-row">
                                <div class="results-stat">
                                    <div class="results-stat-val" style="color:var(--green)">{{ $aciertos }}
                                    </div>
                                    <div class="results-stat-label">Correctas</div>
                                </div>
                                <div class="results-stat">
                                    <div class="results-stat-val" style="color:var(--red)">{{ $errores }}</div>
                                    <div class="results-stat-label">Incorrectas</div>
                                </div>
                                <div class="results-stat">
                                    <div class="results-stat-val" style="color:var(--text-3)">{{ $sinResp }}
                                    </div>
                                    <div class="results-stat-label">Sin responder</div>
                                </div>
                            </div>
                        </div>

                        <p class="results-section-title">Revisión de respuestas</p>

                        <div class="questions-list">
                            @foreach ($resultsData as $rd)
                                @php
                                    $cardClass = $rd['sin_respuesta']
                                        ? 'res-unanswered'
                                        : ($rd['es_correcta']
                                            ? 'res-correct'
                                            : 'res-wrong');
                                    $badgeClass = $rd['sin_respuesta']
                                        ? 'unanswered'
                                        : ($rd['es_correcta']
                                            ? 'correct'
                                            : 'wrong');
                                    $badgeText = $rd['sin_respuesta']
                                        ? 'Sin responder'
                                        : ($rd['es_correcta']
                                            ? 'Correcto'
                                            : 'Incorrecto');
                                @endphp
                                <div class="q-card {{ $cardClass }}" id="q-{{ $rd['question']->id }}">
                                    <div class="q-header">
                                        <span class="q-num-pill">Pregunta {{ $rd['numero'] }}</span>
                                        <span class="q-pts">{{ $rd['question']->puntos }}
                                            pt{{ $rd['question']->puntos != 1 ? 's' : '' }}</span>
                                        <span class="q-result-badge {{ $badgeClass }}">
                                            @if ($rd['es_correcta'])
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                                    style="width:11px;height:11px">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            @elseif ($rd['sin_respuesta'])
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                                    style="width:11px;height:11px">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                                    style="width:11px;height:11px">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            @endif
                                            {{ $badgeText }}
                                        </span>
                                    </div>
                                    <div class="q-text">{{ $rd['question']->texto_pregunta }}</div>
                                    @if ($rd['question']->imagen_path)
                                        <img src="{{ Storage::url($rd['question']->imagen_path) }}"
                                            alt="Imagen pregunta {{ $rd['numero'] }}" class="q-img" loading="lazy">
                                    @endif
                                    <div
                                        class="options {{ $rd['question']->options->whereNotNull('imagen_path')->count() > 0 ? 'single-col' : '' }}">
                                        @foreach ($rd['question']->options as $option)
                                            @php
                                                $isCorrecta = $rd['correcta'] && $option->id === $rd['correcta']->id;
                                                $isRespondida =
                                                    $rd['respondida'] && $option->id === $rd['respondida']->id;
                                                $optClass = 'opt-readonly';
                                                if ($isCorrecta) {
                                                    $optClass = 'opt-correct';
                                                } elseif ($isRespondida && !$isCorrecta) {
                                                    $optClass = 'opt-wrong';
                                                }
                                            @endphp
                                            <button class="opt {{ $optClass }}" style="cursor:default;" disabled>
                                                <div class="opt-letter">{{ chr(64 + $loop->iteration) }}</div>
                                                <div class="opt-body">
                                                    <div class="opt-text">{{ $option->texto_opcion }}</div>
                                                    @if ($isCorrecta && $isRespondida)
                                                        <div
                                                            style="font-size:0.65rem;color:var(--green);font-weight:700;margin-top:4px;">
                                                            ✓ Tu respuesta · Correcta</div>
                                                    @elseif ($isCorrecta)
                                                        <div
                                                            style="font-size:0.65rem;color:var(--green);font-weight:700;margin-top:4px;">
                                                            ✓ Respuesta correcta</div>
                                                    @elseif ($isRespondida)
                                                        <div
                                                            style="font-size:0.65rem;color:var(--red);font-weight:700;margin-top:4px;">
                                                            ✗ Tu respuesta</div>
                                                    @endif
                                                    @if ($option->imagen_path)
                                                        <img src="{{ Storage::url($option->imagen_path) }}"
                                                            alt="Opción {{ chr(64 + $loop->iteration) }}"
                                                            class="opt-img" loading="lazy">
                                                    @endif
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ── Footer fijo ── --}}
    <div class="exam-footer">

        {{-- Footer modo EXAMEN --}}
        <div class="footer-exam {{ $showResults ? 'hidden' : '' }}" id="footer-exam"
            style="{{ $showResults ? 'display:none;' : 'display:contents;' }}">
            @if ($isPreview)
                <button class="btn btn-preview-back" wire:click="submitExam">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" style="width:13px;height:13px">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver al curso
                </button>
                <span class="footer-info">Vista previa — no se registra intento</span>
                <span></span>
            @else
                <button class="btn btn-abandon" wire:click="mountAction('confirmAbandon')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" style="width:13px;height:13px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                    Abandonar
                </button>
                <span class="footer-info" id="footer-info">0 / {{ $this->exam->questions->count() }}
                    respondidas</span>
                <button class="btn btn-submit" id="submit-btn" onclick="showProcessingOverlay()"
                    wire:click="mountAction('confirmSubmit')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" style="width:13px;height:13px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Entregar examen
                </button>
            @endif
        </div>

        {{-- Footer modo RESULTADOS --}}
        <div class="footer-results {{ $showResults ? 'visible' : '' }}" id="footer-results"
            style="{{ $showResults ? 'display:contents;' : 'display:none;' }}">
            <span></span>
            <span class="footer-info" id="footer-result-msg">
                {{ $showResults ? ($aprobado ? '¡Felicidades, aprobaste!' : 'No alcanzaste el puntaje mínimo de ' . $puntajeMin . ' pts') : '' }}
            </span>
            <button class="btn btn-back-course" wire:click="goBackToCourse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" style="width:13px;height:13px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al curso
            </button>
        </div>

    </div>

    <x-filament-actions::modals />

    <script>
        // ── Constantes ───────────────────────────────────────────
        const TOTAL = {{ $this->exam->questions->count() }};
        const IS_PREVIEW = {{ $isPreview ? 'true' : 'false' }};
        const SHOW_RESULTS = {{ $showResults ? 'true' : 'false' }};
        const PUNTAJE_MIN = {{ $puntajeMin }};
        const prevAnswers = {!! $prevAnswersJson !!};
        const answers = {};

        const ATTEMPT_ID = {{ $this->attempt?->id ?? 'null' }};
        const STORAGE_KEY = `exam_timer_${ATTEMPT_ID}`;
        const SERVER_TIME = {{ $remaining }};

        // ── Si ya cargamos en modo resultados ───────────────────
        if (SHOW_RESULTS) {
            document.addEventListener('DOMContentLoaded', () => switchToResults());
        }

        // ── localStorage timer ───────────────────────────────────
        function getInitialRemaining() {
            if (!ATTEMPT_ID || IS_PREVIEW || SHOW_RESULTS) return SERVER_TIME;
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored !== null) {
                    const parsed = parseInt(stored, 10);
                    if (!isNaN(parsed) && parsed >= 0) return Math.min(parsed, SERVER_TIME);
                }
            } catch (_) {}
            return SERVER_TIME;
        }

        function saveLocalTime() {
            if (!ATTEMPT_ID || IS_PREVIEW) return;
            try {
                localStorage.setItem(STORAGE_KEY, remaining);
            } catch (_) {}
        }

        function clearLocalTime() {
            if (!ATTEMPT_ID) return;
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (_) {}
        }

        let remaining = getInitialRemaining();
        let syncCounter = 0;
        const SYNC_EVERY = 15;
        let exitingControlled = false;
        let timerInterval = null;

        // ── Overlay "Procesando examen" ──────────────────────────
        // Se muestra inmediatamente al confirmar entrega en el modal de Filament.
        // switchToResults() lo oculta cuando el DOM ya tiene los resultados.
        let processingVisible = false;

        function showProcessingOverlay() {
            // El overlay sólo se muestra si el usuario confirma en el modal;
            // aquí sólo armamos el estado interno. La confirmación real
            // la escuchamos vía el evento 'exam-processing' de Livewire.
        }

        function _activateProcessingOverlay() {
            if (processingVisible) return;
            processingVisible = true;

            // Detener timer al instante
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            clearLocalTime();
            exitingControlled = true;

            const ov = document.getElementById('processing-overlay');
            if (ov) ov.classList.add('visible');
        }

        function hideProcessingOverlay() {
            const ov = document.getElementById('processing-overlay');
            if (ov) ov.classList.remove('visible');
            processingVisible = false;
        }

        // ── Cambiar UI a modo resultados ─────────────────────────
        function switchToResults() {
            if (window._resultsShown) return;
            window._resultsShown = true;

            // Apagar timer y overlay
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            clearLocalTime();
            exitingControlled = true;
            hideProcessingOverlay();

            // ── Ocultar todo lo del examen ──────────────────────
            const examBlock = document.getElementById('exam-questions-block');
            if (examBlock) examBlock.style.cssText = 'display:none !important';

            const timerBlock = document.getElementById('sidebar-timer-block');
            if (timerBlock) timerBlock.style.cssText = 'display:none !important';

            const offlineBadge = document.getElementById('offline-badge');
            if (offlineBadge) offlineBadge.style.cssText = 'display:none !important';

            const statsExam = document.getElementById('sidebar-stats-exam');
            if (statsExam) statsExam.style.cssText = 'display:none !important';

            const footerExam = document.getElementById('footer-exam');
            if (footerExam) footerExam.style.cssText = 'display:none !important';

            // ── Mostrar todo lo de resultados ───────────────────
            const resBlock = document.getElementById('results-block');
            if (resBlock) {
                resBlock.style.cssText = 'display:block !important';
                resBlock.classList.add('visible');
            }

            const scoreBlock = document.getElementById('sidebar-score-block');
            if (scoreBlock) scoreBlock.style.cssText =
                'display:flex !important; flex-direction:column; align-items:flex-start; gap:4px;';

            const statsRes = document.getElementById('sidebar-stats-results');
            if (statsRes) statsRes.style.cssText =
                'display:grid !important; grid-template-columns:1fr 1fr; gap:10px;';

            const footerResults = document.getElementById('footer-results');
            if (footerResults) footerResults.style.cssText = 'display:contents !important';
        }

        // ── Seleccionar opción ───────────────────────────────────
        function selectOption(qId, optId, el) {
            if (IS_PREVIEW) return;
            document.querySelectorAll(`.opt[data-qid="${qId}"]`).forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');
            answers[qId] = optId;
            document.getElementById('q-' + qId)?.classList.add('answered');
            document.getElementById('dot-' + qId)?.classList.add('done');
            updateProgress();
            @this.call('saveAnswer', qId, optId);
        }

        function clearAnswer(qId) {
            if (IS_PREVIEW) return;
            document.querySelectorAll(`.opt[data-qid="${qId}"]`).forEach(b => b.classList.remove('selected'));
            delete answers[qId];
            document.getElementById('q-' + qId)?.classList.remove('answered');
            document.getElementById('dot-' + qId)?.classList.remove('done');
            updateProgress();
            @this.call('saveAnswer', qId, null);
        }

        // ── Progreso ─────────────────────────────────────────────
        function updateProgress() {
            const count = Object.keys(answers).length;
            const prog = document.getElementById('prog-text');
            const info = document.getElementById('footer-info');
            const fill = document.getElementById('prog-fill');
            if (prog) prog.textContent = `${count} / ${TOTAL}`;
            if (info) info.textContent = `${count} / ${TOTAL} respondidas`;
            if (fill) fill.style.width = `${TOTAL > 0 ? Math.round(count / TOTAL * 100) : 0}%`;
        }

        // ── Timer ────────────────────────────────────────────────
        function tick() {
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            const el = document.getElementById('exam-timer');
            if (!el) return;

            el.textContent = `${m}:${s}`;
            el.classList.remove('warning', 'danger');
            if (remaining <= 60) el.classList.add('danger');
            else if (remaining <= 300) el.classList.add('warning');

            if (remaining <= 0) {
                clearInterval(timerInterval);
                timerInterval = null;
                clearLocalTime();
                const btn = document.getElementById('submit-btn');
                if (btn) btn.disabled = true;
                if (!IS_PREVIEW) {
                    _activateProcessingOverlay();
                    @this.call('expireExam');
                }
                return;
            }

            remaining--;
            saveLocalTime();

            syncCounter++;
            if (!IS_PREVIEW && syncCounter >= SYNC_EVERY) {
                syncCounter = 0;
                @this.call('syncTimer', remaining);
            }
        }

        // ── Eventos Livewire ─────────────────────────────────────
        document.addEventListener('livewire:initialized', () => {

            // PHP dispara 'exam-processing' justo antes de calcular → mostrar overlay
            Livewire.on('exam-processing', () => {
                _activateProcessingOverlay();
            });

            // PHP dispara 'exam-submitted' cuando ya terminó de calcular → switch UI
            Livewire.on('exam-submitted', () => {
                switchToResults();
            });
        });

        // Fallback: detectar cambio de DOM si los eventos JS no llegaron
        document.addEventListener('livewire:updated', () => {
            const resBlock = document.getElementById('results-block');
            if (resBlock && resBlock.querySelector('.results-hero')) {
                switchToResults();
            }
        });

        // ── Online / Offline ─────────────────────────────────────
        function setOnlineStatus(online) {
            const badge = document.getElementById('offline-badge');
            if (badge) badge.classList.toggle('visible', !online);
            if (online && !IS_PREVIEW) @this.call('syncTimer', remaining);
        }
        window.addEventListener('online', () => setOnlineStatus(true));
        window.addEventListener('offline', () => setOnlineStatus(false));

        document.addEventListener('visibilitychange', () => {
            if (!IS_PREVIEW && document.visibilityState === 'hidden' && remaining > 0) {
                saveLocalTime();
                @this.call('syncTimer', remaining);
            }
        });

        window.addEventListener('beforeunload', e => {
            saveLocalTime();
            if (!IS_PREVIEW && !exitingControlled) {
                e.preventDefault();
                e.returnValue = 'Si cierras la página el tiempo seguirá corriendo. ¿Deseas salir?';
            }
        });

        document.addEventListener('livewire:navigating', () => {
            exitingControlled = true;
        });

        // ── Restaurar respuestas previas ─────────────────────────
        function restorePrevAnswers() {
            Object.entries(prevAnswers).forEach(([qId, optId]) => {
                if (!optId) return;
                const btn = document.querySelector(`.opt[data-qid="${qId}"][data-oid="${optId}"]`);
                if (btn) {
                    answers[qId] = parseInt(optId);
                    btn.classList.add('selected');
                    document.getElementById('q-' + qId)?.classList.add('answered');
                    document.getElementById('dot-' + qId)?.classList.add('done');
                }
            });
            updateProgress();
        }

        // ── Init ─────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            if (!SHOW_RESULTS) {
                restorePrevAnswers();
                if (!IS_PREVIEW) {
                    timerInterval = setInterval(tick, 1000);
                    tick();
                }
                setOnlineStatus(navigator.onLine);
            }
        });
    </script>

</x-filament-panels::page>
