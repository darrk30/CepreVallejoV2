<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; }

        .exam-root {
            --brand:        #2563eb;
            --brand-dark:   #1d4ed8;
            --brand-soft:   rgba(37,99,235,.09);
            --brand-border: rgba(37,99,235,.22);
            --green:        #059669;
            --green-soft:   rgba(5,150,105,.1);
            --green-border: rgba(5,150,105,.28);
            --amber:        #d97706;
            --amber-soft:   rgba(217,119,6,.1);
            --red:          #dc2626;
            --red-soft:     rgba(220,38,38,.08);
            --surface:      #ffffff;
            --surface-2:    #f4f6fb;
            --surface-3:    #eef0f7;
            --border:       #e2e5ef;
            --border-dark:  #c8ccdc;
            --text:         #111827;
            --text-2:       #4b5563;
            --text-3:       #9ca3af;
            --opt-sel-bg:     #2563eb;
            --opt-sel-text:   #ffffff;
            --opt-sel-border: #1d4ed8;
            --radius:    14px;
            --radius-sm: 9px;
        }

        .dark .exam-root {
            --surface:      #111827;
            --surface-2:    #1f2937;
            --surface-3:    #374151;
            --border:       #374151;
            --border-dark:  #4b5563;
            --text:         #f9fafb;
            --text-2:       #d1d5db;
            --text-3:       #6b7280;
            --opt-sel-bg:     #3b82f6;
            --opt-sel-text:   #ffffff;
            --opt-sel-border: #2563eb;
        }

        .exam-root {
            margin: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ══ BADGES ══ */
        .exam-meta-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .exam-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 99px;
            border: 1px solid;
        }

        .badge-blue  { background: var(--brand-soft); color: var(--brand); border-color: var(--brand-border); }
        .badge-green { background: var(--green-soft); color: var(--green); border-color: var(--green-border); }
        .badge-amber { background: var(--amber-soft); color: var(--amber); border-color: rgba(217,119,6,.3); }
        .badge-red   { background: var(--red-soft);   color: var(--red);   border-color: rgba(220,38,38,.25); }

        /* ══ LAYOUT ══ */
        .exam-body {
            display: grid;
            grid-template-columns: 230px 1fr;
            gap: 20px;
            align-items: start;
        }

        /* ══ SIDEBAR ══ */
        .exam-sidebar {
            position: sticky;
            top: 16px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .sidebar-head {
            padding: 16px 16px 14px;
            border-bottom: 1px solid var(--border);
            background: var(--surface-2);
        }

        .sidebar-exam-title {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text);
            margin: 0 0 2px;
            line-height: 1.3;
            letter-spacing: -.01em;
        }

        .sidebar-exam-sub {
            font-size: 0.68rem;
            color: var(--text-3);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Timer */
        .sidebar-timer {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .sidebar-timer-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-3);
        }

        .sidebar-timer-val {
            font-family: 'Fira Code', monospace;
            font-size: 1.55rem;
            font-weight: 500;
            color: var(--brand);
            line-height: 1;
        }

        .sidebar-timer-val.warning { color: var(--amber); }
        .sidebar-timer-val.danger  { color: var(--red); animation: blink-red 1s ease-in-out infinite; }

        @keyframes blink-red {
            0%,100% { opacity: 1; }
            50%      { opacity: .4; }
        }

        /* Indicador offline */
        .offline-badge {
            display: none;
            align-items: center;
            gap: 5px;
            background: var(--amber-soft);
            border: 1px solid rgba(217,119,6,.3);
            border-radius: 99px;
            padding: 3px 10px;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--amber);
            margin: 0 16px 0;
        }

        .offline-badge.visible { display: inline-flex; }

        /* Progreso */
        .sidebar-prog {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-prog-top {
            display: flex;
            justify-content: space-between;
            font-size: 0.67rem;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 7px;
        }

        .sidebar-prog-track {
            height: 6px;
            background: var(--surface-3);
            border-radius: 99px;
            overflow: hidden;
        }

        .sidebar-prog-fill {
            height: 100%;
            background: var(--brand);
            border-radius: 99px;
            transition: width .45s cubic-bezier(.4,0,.2,1);
        }

        /* Nav */
        .sidebar-body { padding: 14px 16px; }

        .sidebar-label {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-3);
            margin: 0 0 10px;
        }

        .exam-nav {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            margin-bottom: 16px;
        }

        .exam-nav-dot {
            aspect-ratio: 1;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: var(--surface-2);
            font-size: 11px;
            font-weight: 700;
            color: var(--text-2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
            user-select: none;
        }

        .exam-nav-dot:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-soft); }
        .exam-nav-dot.done  { background: var(--green-soft); border-color: var(--green-border); color: var(--green); }

        .sidebar-divider { border: none; border-top: 1px solid var(--border); margin: 14px 0; }

        .sidebar-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        .sidebar-stat-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 12px;
        }

        .sidebar-stat-card-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-3);
            margin: 0 0 4px;
        }

        .sidebar-stat-card-val { font-size: 1.25rem; font-weight: 800; color: var(--text); line-height: 1; }
        .sidebar-stat-card-sub { font-size: 0.62rem; color: var(--text-3); margin-top: 3px; }

        /* ══ PREGUNTAS ══ */
        .questions-list { display: flex; flex-direction: column; gap: 14px; }

        .q-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 26px;
            position: relative;
            transition: border-color .2s;
        }

        .q-card::before {
            content: '';
            position: absolute;
            left: 0; top: 14px; bottom: 14px;
            width: 3.5px;
            background: var(--border-dark);
            border-radius: 0 4px 4px 0;
            transition: background .25s;
        }

        .q-card.answered { border-color: var(--green-border); }
        .q-card.answered::before { background: var(--green); }

        .q-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .q-num-pill {
            background: var(--brand-soft);
            color: var(--brand);
            border: 1px solid var(--brand-border);
            border-radius: 7px;
            padding: 3px 10px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .q-pts {
            background: var(--amber-soft);
            color: var(--amber);
            border: 1px solid rgba(217,119,6,.25);
            border-radius: 99px;
            padding: 3px 10px;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .q-clear-btn {
            margin-left: auto;
            background: transparent;
            border: 1px solid var(--border-dark);
            border-radius: 7px;
            padding: 3px 10px;
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-3);
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: none;
            align-items: center;
            gap: 5px;
            transition: all .15s;
        }

        .q-clear-btn:hover { border-color: var(--red); color: var(--red); background: var(--red-soft); }
        .q-card.answered .q-clear-btn { display: inline-flex; }

        .q-text {
            font-size: 0.93rem;
            line-height: 1.65;
            color: var(--text);
            margin-bottom: 18px;
            font-weight: 400;
        }

        .q-img {
            width: 100%;
            max-height: 280px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 18px;
            border: 1px solid var(--border);
        }

        /* ══ OPCIONES ══ */
        .options { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
        .options.single-col { grid-template-columns: 1fr; }

        .opt {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            background: var(--surface-2);
            text-align: left;
            width: 100%;
            transition: border-color .15s, background .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .opt:hover:not(.selected) { border-color: var(--brand-border); background: var(--brand-soft); }
        .opt.selected { border-color: var(--opt-sel-border); background: var(--opt-sel-bg); }
        .opt.selected .opt-text  { color: var(--opt-sel-text); font-weight: 600; }

        .opt-letter {
            width: 27px;
            height: 27px;
            border-radius: 7px;
            border: 1.5px solid var(--border-dark);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-2);
            flex-shrink: 0;
            transition: all .15s;
        }

        .opt.selected .opt-letter { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.4); color: #fff; }

        .opt-body { flex: 1; }

        .opt-text {
            font-size: 0.84rem;
            line-height: 1.5;
            color: var(--text);
            padding-top: 4px;
            transition: color .15s;
        }

        .opt-img {
            width: 100%;
            max-height: 160px;
            object-fit: cover;
            border-radius: 7px;
            margin-top: 8px;
            border: 1px solid var(--border);
        }

        /* ══ ALERTA RETOMAR ══ */
        .resume-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--amber-soft);
            border: 1px solid rgba(217,119,6,.3);
            border-radius: var(--radius-sm);
            padding: 12px 18px;
            margin-bottom: 14px;
            font-size: 0.79rem;
            color: var(--amber);
            font-weight: 600;
        }

        /* ══ FOOTER ══ */
        .exam-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-top: 1.5px solid var(--border);
            padding: 13px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            z-index: 50;
        }

        .dark .exam-footer { background: rgba(17,24,39,.96); }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            border: 1.5px solid;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all .15s;
        }

        .btn-abandon { background: transparent; border-color: rgba(220,38,38,.35); color: var(--red); }
        .btn-abandon:hover { background: var(--red-soft); border-color: var(--red); }

        .btn-submit { background: var(--brand); border-color: var(--brand); color: #ffffff !important; }
        .btn-submit:hover { background: var(--brand-dark); border-color: var(--brand-dark); box-shadow: 0 4px 16px rgba(37,99,235,.35); }
        .btn-submit:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }

        .footer-info { font-size: 0.73rem; color: var(--text-2); text-align: center; font-weight: 500; }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 800px) {
            .exam-body { grid-template-columns: 1fr; }
            .exam-sidebar { position: static; }
            .q-card { padding: 16px 18px; }
            .options { grid-template-columns: 1fr; }
            .exam-footer { padding: 10px 16px; }
        }

        @media (max-width: 480px) {
            .exam-nav { grid-template-columns: repeat(6, 1fr); }
        }
    </style>

    @php
        $prevAnswers  = $this->attempt->respuestas_enviadas ?? [];
        $prevAnswersJson = json_encode((object) $prevAnswers);
        $isResume     = count($prevAnswers) > 0;
        $remaining    = $this->getRemainingSeconds();
        $displayM     = str_pad(intdiv($remaining, 60), 2, '0', STR_PAD_LEFT);
        $displayS     = str_pad($remaining % 60,       2, '0', STR_PAD_LEFT);
        $intentosRestantes = $this->intentos_restantes;
        $intentosMaximos   = $this->exam->intentos_maximos;
    @endphp

    <div class="exam-root">

        {{-- BADGES --}}
        <div class="exam-meta-bar">
            <span class="exam-badge badge-blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                {{ $this->exam->questions->count() }} preguntas
            </span>
            <span class="exam-badge badge-amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                {{ $this->exam->questions->sum('puntos') }} puntos
            </span>
            <span class="exam-badge badge-green">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Aprobar con {{ $this->exam->puntaje_minimo }} pts
            </span>
            @if ($intentosMaximos > 1)
                <span class="exam-badge badge-red">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    {{ $intentosRestantes }} intento{{ $intentosRestantes != 1 ? 's' : '' }} restante{{ $intentosRestantes != 1 ? 's' : '' }}
                </span>
            @endif
            @if ($isResume)
                <span class="exam-badge badge-amber">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.374-12.75a2.25 2.25 0 0 1 3.9 0l7.33 12.75Z"/></svg>
                    Retomando intento
                </span>
            @endif
        </div>

        {{-- LAYOUT 2 COLUMNAS --}}
        <div class="exam-body">

            {{-- ══ SIDEBAR ══ --}}
            <div class="exam-sidebar" wire:ignore>

                <div class="sidebar-head">
                    <p class="sidebar-exam-title">{{ $this->exam->titulo }}</p>
                    <p class="sidebar-exam-sub">
                        {{ $this->exam->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->nombre ?? '' }}
                        @if ($this->exam->detail?->titulo)
                            &middot; {{ $this->exam->detail->titulo }}
                        @endif
                    </p>
                </div>

                <div class="sidebar-timer">
                    <span class="sidebar-timer-label">Tiempo restante</span>
                    <span class="sidebar-timer-val" id="exam-timer">{{ $displayM }}:{{ $displayS }}</span>
                </div>

                {{-- Indicador sin conexión --}}
                <div id="offline-badge" class="offline-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M6.111 6.89A6.992 6.992 0 003 12c0 1.657.575 3.176 1.527 4.368m4.167-8.586A5 5 0 0117 12c0 .74-.16 1.44-.448 2.073M9.764 9.766A3 3 0 0115 12m-3 3h.01"/></svg>
                    Sin conexión — tiempo guardado
                </div>

                <div class="sidebar-prog">
                    <div class="sidebar-prog-top">
                        <span>Progreso</span>
                        <span id="prog-text">0 / {{ $this->exam->questions->count() }}</span>
                    </div>
                    <div class="sidebar-prog-track">
                        <div class="sidebar-prog-fill" id="prog-fill" style="width:0%"></div>
                    </div>
                </div>

                <div class="sidebar-body">
                    <p class="sidebar-label">Preguntas</p>
                    <div class="exam-nav">
                        @foreach ($this->exam->questions as $qi => $q)
                            <div class="exam-nav-dot" id="dot-{{ $q->id }}" title="Pregunta {{ $qi + 1 }}"
                                onclick="document.getElementById('q-{{ $q->id }}').scrollIntoView({behavior:'smooth',block:'start'})">
                                {{ $qi + 1 }}
                            </div>
                        @endforeach
                    </div>

                    <hr class="sidebar-divider">

                    <div class="sidebar-stats">
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Puntaje</p>
                            <div class="sidebar-stat-card-val">{{ $this->exam->questions->sum('puntos') }}</div>
                            <div class="sidebar-stat-card-sub">{{ $this->exam->puntaje_minimo }} para aprobar</div>
                        </div>
                        <div class="sidebar-stat-card">
                            <p class="sidebar-stat-card-label">Duración</p>
                            <div class="sidebar-stat-card-val">{{ $this->exam->duracion_minutos }}<span style="font-size:.75rem;font-weight:500;color:var(--text-3);margin-left:2px;">min</span></div>
                            <div class="sidebar-stat-card-sub">{{ $this->exam->questions->count() }} preguntas</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ PREGUNTAS ══ --}}
            <div class="questions-list" wire:ignore>

                @if ($isResume)
                    <div class="resume-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        Estás retomando un intento en progreso. Tus respuestas anteriores han sido recuperadas.
                    </div>
                @endif

                @foreach ($this->exam->questions as $qi => $question)
                    <div class="q-card" id="q-{{ $question->id }}" data-qid="{{ $question->id }}">

                        <div class="q-header">
                            <span class="q-num-pill">Pregunta {{ $qi + 1 }}</span>
                            <span class="q-pts">{{ $question->puntos }} pt{{ $question->puntos != 1 ? 's' : '' }}</span>
                            <button class="q-clear-btn" id="clear-{{ $question->id }}"
                                onclick="clearAnswer({{ $question->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                Limpiar
                            </button>
                        </div>

                        <div class="q-text">{{ $question->texto_pregunta }}</div>

                        @if ($question->imagen_path)
                            <img src="{{ Storage::url($question->imagen_path) }}" alt="Imagen pregunta {{ $qi + 1 }}" class="q-img" loading="lazy">
                        @endif

                        <div class="options {{ $question->options->whereNotNull('imagen_path')->count() > 0 ? 'single-col' : '' }}">
                            @foreach ($question->options as $option)
                                <button class="opt"
                                    id="opt-{{ $option->id }}"
                                    data-qid="{{ $question->id }}"
                                    data-oid="{{ $option->id }}"
                                    onclick="selectOption({{ $question->id }}, {{ $option->id }}, this)">
                                    <div class="opt-letter">{{ chr(64 + $loop->iteration) }}</div>
                                    <div class="opt-body">
                                        <div class="opt-text">{{ $option->texto_opcion }}</div>
                                        @if ($option->imagen_path)
                                            <img src="{{ Storage::url($option->imagen_path) }}" alt="Opción {{ chr(64 + $loop->iteration) }}" class="opt-img" loading="lazy">
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="exam-footer">
        <button class="btn btn-abandon" onclick="confirmAbandon()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            Abandonar
        </button>

        <span class="footer-info" id="footer-info">0 / {{ $this->exam->questions->count() }} respondidas</span>

        <button class="btn btn-submit" id="submit-btn" wire:click="submitExam">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            Entregar examen
        </button>
    </div>

    <x-filament-actions::modals />

    <script>
        const TOTAL              = {{ $this->exam->questions->count() }};
        const ATTEMPT_ID         = {{ $this->attempt->id }};
        const INTENTOS_RESTANTES = {{ $intentosRestantes }};
        const INTENTOS_MAXIMOS   = {{ $intentosMaximos }};
        const prevAnswers        = {!! $prevAnswersJson !!};
        const answers            = {};

        // ── Tiempo: arranca desde el valor persistido en BD ──────
        let remaining    = {{ $remaining }};
        let isOffline    = false;
        // Sincronizar cada 20 segundos (guarda en BD)
        const SYNC_EVERY = 20;
        let syncCounter  = 0;

        // ── Seleccionar opción ───────────────────────────────────
        function selectOption(qId, optId, el) {
            document.querySelectorAll(`.opt[data-qid="${qId}"]`).forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');
            answers[qId] = optId;
            document.getElementById('q-' + qId)?.classList.add('answered');
            document.getElementById('dot-' + qId)?.classList.add('done');
            updateProgress();
            @this.call('saveAnswer', qId, optId);
        }

        // ── Limpiar respuesta ────────────────────────────────────
        function clearAnswer(qId) {
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
            document.getElementById('prog-text').textContent   = `${count} / ${TOTAL}`;
            document.getElementById('footer-info').textContent = `${count} / ${TOTAL} respondidas`;
            document.getElementById('prog-fill').style.width   = `${Math.round(count / TOTAL * 100)}%`;
        }

        // ── Confirmar abandono con mensaje según intentos ────────
        function confirmAbandon() {
            let msg;
            if (INTENTOS_RESTANTES <= 1) {
                // Último intento disponible → advertir que se entregará
                msg = '⚠️ Este es tu último intento.\n\n'
                    + 'Si abandonas, el examen se entregará automáticamente con las respuestas que llevas hasta ahora.\n\n'
                    + '¿Deseas entregar el examen?';
            } else {
                // Tiene más intentos
                const restantesDespues = INTENTOS_RESTANTES - 1;
                msg = '¿Seguro que quieres abandonar el examen?\n\n'
                    + `Perderás este intento. Te quedarán ${restantesDespues} intento${restantesDespues !== 1 ? 's' : ''} disponible${restantesDespues !== 1 ? 's' : ''}.\n\n`
                    + '¿Confirmas que deseas abandonar?';
            }

            if (confirm(msg)) {
                clearInterval(timerInterval);
                @this.call('abandonExam');
            }
        }

        // ── Temporizador ─────────────────────────────────────────
        function tick() {
            // Actualizar display
            const m  = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s  = String(remaining % 60).padStart(2, '0');
            const el = document.getElementById('exam-timer');
            if (!el) return;

            el.textContent = `${m}:${s}`;
            el.classList.remove('warning', 'danger');
            if (remaining <= 60)       el.classList.add('danger');
            else if (remaining <= 300) el.classList.add('warning');

            // Tiempo agotado
            if (remaining <= 0) {
                clearInterval(timerInterval);
                document.getElementById('submit-btn').disabled = true;
                @this.call('expireExam');
                return;
            }

            remaining--;
            syncCounter++;

            // Sincronizar con el servidor cada SYNC_EVERY segundos
            if (syncCounter >= SYNC_EVERY) {
                syncCounter = 0;
                @this.call('syncTimer', remaining);
            }
        }

        const timerInterval = setInterval(tick, 1000);

        // ── Detectar online / offline ────────────────────────────
        function setOnlineStatus(online) {
            isOffline = !online;
            const badge = document.getElementById('offline-badge');
            if (badge) badge.classList.toggle('visible', !online);
        }

        window.addEventListener('online',  () => setOnlineStatus(true));
        window.addEventListener('offline', () => setOnlineStatus(false));

        // ── Prevenir cierre/recarga accidental ───────────────────
        window.addEventListener('beforeunload', e => {
            // Sincronizar tiempo antes de salir (best-effort)
            navigator.sendBeacon(
                '/livewire/message/take-exam',   // Livewire endpoint — ajusta si es diferente
                JSON.stringify({ fingerprint: {}, serverMemo: {}, updates: [] })
            );
            e.preventDefault();
            e.returnValue = '';
        });

        // También sincronizar al hacer visibilitychange (cambio de pestaña)
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden' && remaining > 0) {
                @this.call('syncTimer', remaining);
            }
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
            restorePrevAnswers();
            tick();
            setOnlineStatus(navigator.onLine);
        });
    </script>

</x-filament-panels::page>