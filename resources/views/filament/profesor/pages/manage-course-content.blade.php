<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

        /* ══════════════════════════════════════
       VARIABLES
    ══════════════════════════════════════ */
        .aula {
            --surface: #ffffff;
            --surface2: #f9f8ff;
            --surface3: #eeecfb;
            --border: rgba(99, 102, 241, 0.14);
            --border-m: rgba(99, 102, 241, 0.28);
            --border-s: rgba(99, 102, 241, 0.07);
            --text1: #0c0b1a;
            --text2: #3b3866;
            --muted: #8b88b0;
            --accent: #5b5ef4;
            --accent-h: #4340d0;
            --accent-gl: rgba(91, 94, 244, 0.18);
            --accent-bg: rgba(91, 94, 244, 0.07);
            --green: #059669;
            --green-bg: rgba(5, 150, 105, 0.08);
            --green-bd: rgba(5, 150, 105, 0.22);
            --amber: #d97706;
            --amber-bg: rgba(217, 119, 6, 0.08);
            --amber-bd: rgba(217, 119, 6, 0.22);
            --red: #dc2626;
            --red-bg: rgba(220, 38, 38, 0.07);
            --red-bd: rgba(220, 38, 38, 0.20);
            --handle: #c4c2e0;
            --sh-sm: 0 1px 4px rgba(79, 70, 229, 0.07), 0 1px 2px rgba(0, 0, 0, 0.04);
            --sh-md: 0 4px 16px rgba(79, 70, 229, 0.10), 0 2px 6px rgba(0, 0, 0, 0.05);
            --sh-drop: 0 8px 24px rgba(79, 70, 229, 0.13), 0 2px 8px rgba(0, 0, 0, 0.08);
            --r: 14px;
        }

        .dark .aula {
            --surface: #151424;
            --surface2: #1b1a2e;
            --surface3: #221f3a;
            --border: rgba(129, 140, 248, 0.12);
            --border-m: rgba(129, 140, 248, 0.28);
            --border-s: rgba(129, 140, 248, 0.06);
            --text1: #edecf8;
            --text2: #a9a6cc;
            --muted: #636086;
            --accent: #818cf8;
            --accent-h: #a5b4fc;
            --accent-gl: rgba(129, 140, 248, 0.22);
            --accent-bg: rgba(129, 140, 248, 0.08);
            --green: #34d399;
            --green-bg: rgba(52, 211, 153, 0.09);
            --green-bd: rgba(52, 211, 153, 0.22);
            --amber: #fbbf24;
            --amber-bg: rgba(251, 191, 36, 0.09);
            --amber-bd: rgba(251, 191, 36, 0.22);
            --red: #f87171;
            --red-bg: rgba(248, 113, 113, 0.09);
            --red-bd: rgba(248, 113, 113, 0.22);
            --handle: #3d3a5c;
            --sh-sm: 0 1px 4px rgba(0, 0, 0, 0.28);
            --sh-md: 0 4px 16px rgba(0, 0, 0, 0.38);
            --sh-drop: 0 8px 24px rgba(0, 0, 0, 0.45), 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .aula {
            font-family: 'Bricolage Grotesque', sans-serif;
            color: var(--text1);
            padding: 0 0 60px;
        }

        /* ── BANNER ── */
        .aula-banner {
            background: var(--surface) !important;
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 18px 22px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--sh-sm);
        }

        .aula-banner-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--accent-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .aula-banner-icon svg {
            width: 20px;
            height: 20px;
            color: var(--accent) !important;
        }

        .aula-banner-info h2 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text1) !important;
            margin: 0 0 2px;
        }

        .aula-banner-info p {
            font-size: 0.76rem;
            color: var(--muted) !important;
            margin: 0;
        }

        /* ── TOOLBAR ── */
        .aula-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .aula-count {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--muted) !important;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ── BOTONES SORT MODE (toggle) ── */
        .btn-sort {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--accent) !important;
            background: var(--accent-bg) !important;
            border: 1px solid var(--border-m) !important;
            border-radius: 8px;
            padding: 6px 13px;
            cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
            font-family: 'Bricolage Grotesque', sans-serif;
            white-space: nowrap;
        }

        .btn-sort:hover {
            background: var(--accent-gl) !important;
        }

        .btn-sort.active {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
        }

        .btn-sort.active:hover {
            background: var(--accent-h) !important;
        }

        .btn-sort svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        /* ── BOTÓN CONFIRMAR ── */
        .btn-confirm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fff !important;
            background: var(--green) !important;
            border: 1px solid transparent !important;
            border-radius: 8px;
            padding: 6px 13px;
            cursor: pointer;
            transition: opacity .15s;
            font-family: 'Bricolage Grotesque', sans-serif;
            white-space: nowrap;
        }

        .btn-confirm:hover {
            opacity: .85;
        }

        .btn-confirm svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        /* ══════════════════════════════════════
           BOTONES ICONO — DESKTOP
        ══════════════════════════════════════ */

        /* Wrapper de acciones */
        .aula-actions {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .detail-actions {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
            align-items: flex-start;
            padding-top: 1px;
        }

        /* Botón icono base */
        .icon-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--surface2);
            transition: background .15s, border-color .15s, transform .1s, box-shadow .15s;
            flex-shrink: 0;
            padding: 0;
        }

        .icon-btn:hover {
            background: var(--accent-bg);
            border-color: var(--border-m);
            box-shadow: var(--sh-sm);
        }

        .icon-btn:active {
            transform: scale(.91);
        }

        .icon-btn svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            pointer-events: none;
        }

        /* Variantes de color */
        .icon-btn.btn-add {
            color: var(--accent);
            background: var(--accent-bg);
            border-color: var(--border-m);
        }

        .icon-btn.btn-edit {
            color: var(--amber);
            background: var(--amber-bg);
            border-color: var(--amber-bd);
        }

        .icon-btn.btn-del {
            color: var(--red);
            background: var(--red-bg);
            border-color: var(--red-bd);
        }

        .icon-btn.btn-sort-icon {
            color: var(--accent);
            background: var(--accent-bg);
            border-color: var(--border-m);
        }

        /* Estado activo — cuando ordenar está encendido */
        .icon-btn.active {
            background: var(--accent) !important;
            color: #fff !important;
            border-color: var(--accent) !important;
        }

        .icon-btn.active:hover {
            background: var(--accent-h) !important;
        }

        .icon-btn.btn-add:hover {
            background: var(--accent-gl);
        }

        .icon-btn.btn-edit:hover {
            opacity: .85;
        }

        .icon-btn.btn-del:hover {
            opacity: .85;
        }

        /* ── TOOLTIP (solo dispositivos con hover) ── */
        @media (hover: hover) {

            .icon-btn[data-tip]::before,
            .icon-btn[data-tip]::after {
                pointer-events: none;
                position: absolute;
                left: 50%;
                opacity: 0;
                transition: opacity .15s, transform .15s;
            }

            /* Burbuja */
            .icon-btn[data-tip]::after {
                content: attr(data-tip);
                bottom: calc(100% + 8px);
                transform: translateX(-50%) translateY(4px);
                background: var(--text1);
                color: var(--surface);
                font-family: 'Bricolage Grotesque', sans-serif;
                font-size: 0.64rem;
                font-weight: 600;
                padding: 4px 8px;
                border-radius: 6px;
                white-space: nowrap;
                z-index: 100;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
            }

            /* Flechita */
            .icon-btn[data-tip]::before {
                content: '';
                bottom: calc(100% + 3px);
                transform: translateX(-50%) translateY(4px);
                border: 5px solid transparent;
                border-top-color: var(--text1);
                z-index: 100;
            }

            .icon-btn[data-tip]:hover::after,
            .icon-btn[data-tip]:hover::before {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Divisor */
        .sec-divider {
            width: 1px;
            height: 18px;
            background: var(--border);
            flex-shrink: 0;
            margin: 0 2px;
        }

        /* ══════════════════════════════════════
           DROPDOWN MÓVIL  ⋯
        ══════════════════════════════════════ */

        /* Ocultar dropdown en desktop, mostrar botones */
        .aula-actions-desktop {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .aula-actions-mobile {
            display: none;
            position: relative;
        }

        @media (max-width: 640px) {
            .aula-actions-desktop {
                display: none !important;
            }

            .aula-actions-mobile {
                display: block;
            }
        }

        /* Botón ⋯ */
        .btn-more {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--surface2);
            transition: background .15s, border-color .15s;
            flex-shrink: 0;
            padding: 0;
        }

        .btn-more:hover {
            background: var(--accent-bg);
            border-color: var(--border-m);
        }

        .btn-more svg {
            width: 14px;
            height: 14px;
            color: var(--muted);
        }

        /* Panel desplegable */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            min-width: 160px;
            background: var(--surface);
            border: 1px solid var(--border-m);
            border-radius: 10px;
            box-shadow: var(--sh-drop);
            z-index: 200;
            overflow: hidden;
            animation: dropIn .15s ease;
        }

        @keyframes dropIn {
            from {
                opacity: 0;
                transform: translateY(-6px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dropdown-menu.open {
            display: block;
        }

        /* Items del dropdown */
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 9px 14px;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text1);
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            transition: background .12s;
            border-bottom: 1px solid var(--border-s);
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: var(--surface2) !important;
        }

        .dropdown-item svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .dropdown-item.item-add {
            color: var(--accent);
        }

        .dropdown-item.item-edit {
            color: var(--amber);
        }

        .dropdown-item.item-del {
            color: var(--red);
        }

        .dropdown-item.item-sort {
            color: var(--accent);
        }

        .dropdown-item.item-cancel {
            color: var(--muted);
        }

        .dropdown-item.item-confirm {
            color: var(--green);
        }

        /* Separador dentro del dropdown */
        .dropdown-sep {
            height: 1px;
            background: var(--border);
            margin: 2px 0;
        }

        /* ── SECTION CARD ── */
        .sec-card {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--r);
            margin-bottom: 14px;
            box-shadow: var(--sh-sm);
            transition: box-shadow .2s, border-color .2s, opacity .15s;
            position: relative;
            overflow: visible;
        }

        .sec-card.dragging {
            opacity: .38;
        }

        .sec-card.drag-over-top::before,
        .sec-card.drag-over-bottom::after {
            content: '';
            display: block;
            height: 3px;
            background: var(--accent);
            border-radius: 4px;
            position: absolute;
            left: 0;
            right: 0;
            z-index: 10;
        }

        .sec-card.drag-over-top::before {
            top: -2px;
        }

        .sec-card.drag-over-bottom::after {
            bottom: -2px;
        }

        /* ── SECTION HEAD ── */
        .sec-head {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-s) !important;
            flex-wrap: wrap;
        }

        /* Handle hamburger */
        .drag-handle {
            display: none;
            flex-direction: column;
            gap: 3px;
            cursor: grab;
            padding: 5px 3px;
            border-radius: 5px;
            transition: background .15s;
            flex-shrink: 0;
            user-select: none;
        }

        .drag-handle.visible {
            display: flex;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .drag-handle span {
            display: block;
            width: 16px;
            height: 2px;
            background: var(--handle) !important;
            border-radius: 2px;
            transition: background .15s;
        }

        .drag-handle:hover {
            background: var(--accent-bg) !important;
        }

        .drag-handle:hover span {
            background: var(--accent) !important;
        }

        .drag-handle.sm span {
            width: 13px;
            height: 1.5px;
        }

        .drag-handle.sm {
            gap: 2.5px;
        }

        .sec-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: var(--accent-bg) !important;
            border-radius: 6px;
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--accent) !important;
            flex-shrink: 0;
        }

        .sec-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text1) !important;
            flex: 1;
            letter-spacing: -0.01em;
            min-width: 80px;
        }

        .sec-meta {
            font-size: 0.68rem;
            color: var(--muted) !important;
            white-space: nowrap;
        }

        /* Descripción de sección */
        .sec-desc {
            font-size: 0.78rem;
            color: var(--text2) !important;
            line-height: 1.5;
            padding: 8px 16px 10px;
            border-bottom: 1px solid var(--border-s) !important;
            background: var(--surface2) !important;
        }

        /* ── SORT INDICATOR BAR ── */
        .sort-bar {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 0.71rem;
            font-weight: 600;
            color: var(--accent) !important;
            background: var(--accent-bg) !important;
            border-top: 1px solid var(--border) !important;
        }

        .sort-bar.visible {
            display: flex;
        }

        .sort-bar svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        .sort-bar span {
            flex: 1;
        }

        /* ── DETAILS ── */
        .details-list {
            padding: 4px 0;
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border-s) !important;
            transition: background .12s;
            position: relative;
        }

        .detail-row:last-child {
            border-bottom: none !important;
        }

        .detail-row:hover {
            background: var(--surface2) !important;
        }

        .detail-row.dragging {
            opacity: .35;
        }

        .detail-row.drag-over-top::before,
        .detail-row.drag-over-bottom::after {
            content: '';
            display: block;
            height: 2px;
            background: var(--accent);
            border-radius: 2px;
            position: absolute;
            left: 16px;
            right: 16px;
            z-index: 10;
        }

        .detail-row.drag-over-top::before {
            top: 0;
        }

        .detail-row.drag-over-bottom::after {
            bottom: 0;
        }

        .detail-num {
            font-family: 'DM Mono', monospace;
            font-size: 0.63rem;
            color: var(--muted) !important;
            min-width: 16px;
            text-align: right;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .detail-content {
            flex: 1;
            min-width: 0;
        }

        .detail-title {
            font-size: 0.87rem;
            font-weight: 600;
            color: var(--text1) !important;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .detail-desc {
            font-size: 0.76rem;
            color: var(--text2) !important;
            line-height: 1.55;
            margin-bottom: 8px;
            white-space: pre-line;
        }

        .detail-resources {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            align-items: center;
        }

        /* Archivo */
        .res-file {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.71rem;
            font-weight: 500;
            font-family: 'DM Mono', monospace;
            color: var(--green) !important;
            background: var(--green-bg) !important;
            border: 1px solid var(--green-bd);
            padding: 3px 10px;
            border-radius: 100px;
            text-decoration: none !important;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity .15s, transform .15s;
        }

        .res-file:hover {
            opacity: .8;
            transform: translateY(-1px);
            text-decoration: none !important;
        }

        .res-file svg {
            width: 11px;
            height: 11px;
            flex-shrink: 0;
        }

        /* Video */
        .res-video {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.71rem;
            font-weight: 600;
            color: var(--accent) !important;
            background: var(--accent-bg) !important;
            border: 1px solid var(--border-m) !important;
            padding: 3px 10px;
            border-radius: 100px;
            cursor: pointer;
            transition: background .15s, transform .15s;
            font-family: 'Bricolage Grotesque', sans-serif;
        }

        .res-video:hover {
            background: var(--accent-gl) !important;
            transform: translateY(-1px);
        }

        .res-video svg {
            width: 12px;
            height: 12px;
        }

        /* iFrame */
        .video-wrap {
            margin-top: 10px;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 16/9;
            max-width: 460px;
            background: #000;
            box-shadow: var(--sh-md);
        }

        .video-wrap iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* ── EMPTY ── */
        .empty-section {
            padding: 18px;
            text-align: center;
            font-size: 0.76rem;
            color: var(--muted) !important;
        }

        .empty-global {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 32px;
            text-align: center;
            background: var(--surface) !important;
            border: 1.5px dashed var(--border-m);
            border-radius: var(--r);
        }

        .empty-global svg {
            width: 46px;
            height: 46px;
            color: var(--accent) !important;
            opacity: .22;
            margin-bottom: 14px;
        }

        .empty-global h3 {
            font-size: 0.93rem;
            font-weight: 700;
            color: var(--text2) !important;
            margin: 0 0 4px;
        }

        .empty-global p {
            font-size: 0.76rem;
            color: var(--muted) !important;
            margin: 0;
        }

        .aula-actions [data-action],
        .detail-actions [data-action] {
            display: none !important;
        }

        /* Ghost de arrastre táctil */
        .touch-ghost {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            opacity: .78;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(79, 70, 229, .18), 0 2px 8px rgba(0, 0, 0, .12);
            background: var(--surface);
            border: 1.5px solid var(--accent);
            padding: 8px 14px;
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text1);
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transform: rotate(1.5deg);
        }
    </style>

    <div class="aula">

        {{-- BANNER --}}
        @if ($this->assignment)
            <div class="aula-banner">
                <div class="aula-banner-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                    </svg>
                </div>
                <div class="aula-banner-info">
                    <h2>{{ $this->assignment->cicloCourse->course->nombre }}</h2>
                    <p>{{ $this->assignment->cicloCourse->academicCycle->nombre ?? 'Ciclo activo' }} · Gestión de
                        contenido</p>
                </div>
            </div>
        @endif

        {{-- TOOLBAR --}}
        <div class="aula-toolbar">
            <span class="aula-count">
                {{ $this->sections->count() }} {{ $this->sections->count() === 1 ? 'sección' : 'secciones' }}
            </span>
            <div style="display:flex;gap:8px;align-items:center;">
                <button class="btn-sort" id="sortSectionsBtn" onclick="toggleSectionSort()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                    </svg>
                    <span id="sortSectionsBtnText">Ordenar secciones</span>
                </button>
                <button class="btn-confirm" id="confirmSectionsBtn" onclick="confirmSectionSort()"
                    style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Guardar orden
                </button>
            </div>
        </div>

        {{-- SECCIONES --}}
        @if ($this->sections->isEmpty())
            <div class="empty-global">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <h3>Sin secciones todavía</h3>
                <p>Crea la primera sección con el botón superior.</p>
            </div>
        @else
            <div id="sectionsContainer">
                @foreach ($this->sections as $si => $section)
                    <div class="sec-card" data-id="{{ $section->id }}">

                        {{-- HEAD --}}
                        <div class="sec-head">

                            <div class="drag-handle sec-handle-global" id="secHandle-{{ $section->id }}"
                                title="Arrastrar para reordenar">
                                <span></span><span></span><span></span>
                            </div>

                            <div class="sec-badge" id="secBadge-{{ $section->id }}">{{ $si + 1 }}</div>
                            <span class="sec-title">{{ $section->titulo }}</span>
                            <span class="sec-meta">{{ $section->details->count() }}
                                tema{{ $section->details->count() !== 1 ? 's' : '' }}</span>

                            {{-- ── ACCIONES DESKTOP ── --}}
                            <div class="aula-actions-desktop">

                                {{-- Ordenar temas --}}
                                <button class="icon-btn btn-sort-icon" id="sortDetailsBtn-{{ $section->id }}"
                                    data-tip="Ordenar temas" onclick="toggleDetailSort({{ $section->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                    </svg>
                                </button>

                                {{-- Confirmar orden temas --}}
                                <button class="icon-btn" id="confirmDetailsBtn-{{ $section->id }}"
                                    data-tip="Guardar orden" onclick="confirmDetailSort({{ $section->id }})"
                                    style="display:none; color:var(--green); background:var(--green-bg); border-color:var(--green-bd);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>

                                <div class="sec-divider"></div>

                                {{-- Nuevo tema --}}
                                <button class="icon-btn btn-add" data-tip="Nuevo tema"
                                    onclick="window.Livewire.dispatch('open-modal', { id: 'createSubtopic-{{ $section->id }}' })"
                                    wire:click="mountAction('createSubtopic', { section_id: {{ $section->id }} })">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>

                                <div class="sec-divider"></div>

                                {{-- Editar sección --}}
                                <button class="icon-btn btn-edit" data-tip="Editar sección"
                                    wire:click="mountAction('editSection', { section_id: {{ $section->id }} })">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                {{-- Eliminar sección --}}
                                <button class="icon-btn btn-del" data-tip="Eliminar sección"
                                    wire:click="mountAction('deleteSection', { section_id: {{ $section->id }} })">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>

                            {{-- ── DROPDOWN MÓVIL ── --}}
                            <div class="aula-actions-mobile">
                                <button class="btn-more"
                                    onclick="toggleDropdown('sec-drop-{{ $section->id }}', event)"
                                    aria-label="Más acciones">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="1.5" />
                                        <circle cx="12" cy="12" r="1.5" />
                                        <circle cx="19" cy="12" r="1.5" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu" id="sec-drop-{{ $section->id }}">

                                    {{-- Ordenar temas / Cancelar --}}
                                    <button class="dropdown-item item-sort"
                                        id="sortDetailsMobile-{{ $section->id }}"
                                        onclick="closeAllDropdowns(); toggleDetailSort({{ $section->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                        </svg>
                                        <span id="sortDetailsMobileText-{{ $section->id }}">Ordenar temas</span>
                                    </button>

                                    {{-- Confirmar orden (solo visible si sort activo) --}}
                                    <button class="dropdown-item item-confirm"
                                        id="confirmDetailsMobile-{{ $section->id }}"
                                        onclick="closeAllDropdowns(); confirmDetailSort({{ $section->id }})"
                                        style="display:none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Guardar orden
                                    </button>

                                    <div class="dropdown-sep"></div>

                                    {{-- Nuevo tema --}}
                                    <button class="dropdown-item item-add"
                                        onclick="closeAllDropdowns(); window.Livewire.dispatch('open-modal', { id: 'createSubtopic-{{ $section->id }}' })"
                                        wire:click="mountAction('createSubtopic', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Nuevo tema
                                    </button>

                                    <div class="dropdown-sep"></div>

                                    {{-- Editar --}}
                                    <button class="dropdown-item item-edit" onclick="closeAllDropdowns()"
                                        wire:click="mountAction('editSection', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                        Editar sección
                                    </button>

                                    {{-- Eliminar --}}
                                    <button class="dropdown-item item-del" onclick="closeAllDropdowns()"
                                        wire:click="mountAction('deleteSection', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Eliminar sección
                                    </button>
                                </div>
                            </div>

                        </div>{{-- /sec-head --}}

                        {{-- DESCRIPCIÓN de sección --}}
                        @if (!empty($section->descripcion))
                            <div class="sec-desc">{{ $section->descripcion }}</div>
                        @endif

                        {{-- SORT BAR --}}
                        <div class="sort-bar" id="sortBar-{{ $section->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                            </svg>
                            <span>Arrastra los temas para reordenar. Pulsa <strong>Guardar orden</strong> cuando
                                termines.</span>
                        </div>

                        {{-- DETALLES --}}
                        <div class="details-list" id="detailsList-{{ $section->id }}">
                            @forelse($section->details as $di => $detail)
                                <div class="detail-row" data-id="{{ $detail->id }}"
                                    data-section="{{ $section->id }}" x-data="{ openVideo: false }">

                                    <div class="drag-handle sm detail-handle-{{ $section->id }}"
                                        title="Arrastrar tema">
                                        <span></span><span></span><span></span>
                                    </div>

                                    <div class="detail-num">{{ $di + 1 }}</div>

                                    <div class="detail-content">
                                        <div class="detail-title">{{ $detail->titulo }}</div>
                                        @if (!empty($detail->descripcion))
                                            <div class="detail-desc">{{ $detail->descripcion }}</div>
                                        @endif
                                        <div class="detail-resources">

                                            @if ($detail->archivo_path)
                                                <a href="{{ Storage::url($detail->archivo_path) }}"
                                                    download="{{ basename($detail->archivo_path) }}" target="_blank"
                                                    class="res-file"
                                                    title="Descargar {{ basename($detail->archivo_path) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                    </svg>
                                                    {{ basename($detail->archivo_path) }}
                                                </a>
                                            @endif

                                            @if ($detail->url_video)
                                                <button class="res-video" @click="openVideo = !openVideo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                                    </svg>
                                                    <span x-text="openVideo ? 'Ocultar video' : 'Ver video'"></span>
                                                </button>
                                            @endif
                                        </div>

                                        @if ($detail->url_video)
                                            <template x-if="openVideo">
                                                <div class="video-wrap">
                                                    <iframe
                                                        x-bind:src="buildEmbedUrl('{{ addslashes($detail->url_video) }}')"
                                                        allowfullscreen
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        referrerpolicy="strict-origin-when-cross-origin">
                                                    </iframe>
                                                </div>
                                            </template>
                                        @endif
                                    </div>

                                    {{-- ── ACCIONES DETALLE DESKTOP ── --}}
                                    <div class="detail-actions aula-actions-desktop">
                                        <button class="icon-btn btn-edit" data-tip="Editar tema"
                                            wire:click="mountAction('editSubtopic', { subtopic_id: {{ $detail->id }} })">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                        <button class="icon-btn btn-del" data-tip="Eliminar tema"
                                            wire:click="mountAction('deleteSubtopic', { subtopic_id: {{ $detail->id }} })">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- ── DROPDOWN DETALLE MÓVIL ── --}}
                                    <div class="aula-actions-mobile" style="padding-top:1px;">
                                        <button class="btn-more"
                                            onclick="toggleDropdown('det-drop-{{ $detail->id }}', event)"
                                            aria-label="Más acciones">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="19" cy="12" r="1.5" />
                                            </svg>
                                        </button>
                                        <div class="dropdown-menu" id="det-drop-{{ $detail->id }}">
                                            <button class="dropdown-item item-edit" onclick="closeAllDropdowns()"
                                                wire:click="mountAction('editSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                                Editar tema
                                            </button>
                                            <button class="dropdown-item item-del" onclick="closeAllDropdowns()"
                                                wire:click="mountAction('deleteSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Eliminar tema
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="empty-section">No hay temas en esta sección aún.</div>
                            @endforelse
                        </div>

                    </div>{{-- /sec-card --}}
                @endforeach
            </div>{{-- /sectionsContainer --}}
        @endif

    </div>

    <x-filament-actions::modals />

    <script>
        /* ══════════════════════════════════════════════════════════
               buildEmbedUrl — convierte cualquier URL de video a embed
               Soporta:
                 · youtube.com/watch?v=ID
                 · youtu.be/ID
                 · youtube.com/shorts/ID
                 · youtube.com/embed/ID  (ya es embed, la devuelve tal cual)
                 · vimeo.com/ID
                 · URL directa de archivo (mp4, webm…) — se devuelve igual
            ══════════════════════════════════════════════════════════ */
        function buildEmbedUrl(url) {
            if (!url) return '';
            url = url.trim();

            // ── YouTube ──
            // Formatos: watch?v=, youtu.be/, /shorts/, /embed/
            const ytMatch = url.match(
                /(?:youtube\.com\/(?:watch\?(?:.*&)?v=|shorts\/|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/
            );
            if (ytMatch) {
                // Preservar timestamp si existe (?t=XX o &t=XX)
                const tMatch = url.match(/[?&]t=(\d+)/);
                const start = tMatch ? `&start=${tMatch[1]}` : '';
                return `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?rel=0&modestbranding=1${start}`;
            }

            // ── Vimeo ──
            const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
            if (vimeoMatch) {
                return `https://player.vimeo.com/video/${vimeoMatch[1]}?dnt=1`;
            }

            // ── URL directa de archivo u otro servicio (devolver tal cual) ──
            return url;
        }

        /* ══════════════════════════════════════════════════════════
           DROPDOWN HELPERS
        ══════════════════════════════════════════════════════════ */
        function toggleDropdown(id, event) {
            event.stopPropagation();
            const target = document.getElementById(id);
            const isOpen = target.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) target.classList.add('open');
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
        }

        // Cerrar al hacer click fuera
        document.addEventListener('click', closeAllDropdowns);

        /* ══════════════════════════════════════════════════════════
           AulaDnD — Drag & Drop para secciones y detalles
        ══════════════════════════════════════════════════════════ */
        const AulaDnD = (function() {

            let secDragSrc = null;
            let detDragSrc = null;
            let secSortOn = false;
            const detSortOn = new Set();

            /* ── Notificación Filament v3 ── */
            function notify(title, status = 'success') {
                try {
                    window.dispatchEvent(new CustomEvent('filament-notification', {
                        detail: {
                            title,
                            status,
                            duration: 4000
                        }
                    }));
                } catch (e) {}
                try {
                    Livewire.dispatch('filament.notifications.send', {
                        notification: {
                            title,
                            status
                        }
                    });
                } catch (e) {}
            }

            /* ══ SECCIONES ══ */
            function bindSection(card) {
                if (card._secBound) return;
                card._secBound = true;

                card.addEventListener('dragstart', e => {
                    if (!card._handleDown) {
                        e.preventDefault();
                        return;
                    }
                    secDragSrc = card;
                    requestAnimationFrame(() => card.classList.add('dragging'));
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', card.dataset.id);
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('dragging');
                    card._handleDown = false;
                    secDragSrc = null;
                    clearDropClasses();
                    refreshSectionBadges();
                });

                card.addEventListener('dragover', e => {
                    if (!secSortOn || !secDragSrc || secDragSrc === card || detDragSrc) return;
                    e.preventDefault();
                    e.stopPropagation();
                    clearCardDrop(card);
                    const mid = card.getBoundingClientRect().top + card.getBoundingClientRect().height / 2;
                    card.classList.add(e.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
                });

                card.addEventListener('dragleave', e => {
                    if (!card.contains(e.relatedTarget)) clearCardDrop(card);
                });

                card.addEventListener('drop', e => {
                    if (!secSortOn || !secDragSrc || secDragSrc === card || detDragSrc) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const container = document.getElementById('sectionsContainer');
                    const mid = card.getBoundingClientRect().top + card.getBoundingClientRect().height / 2;
                    container.insertBefore(secDragSrc, e.clientY < mid ? card : card.nextSibling);
                    clearCardDrop(card);
                    refreshSectionBadges();
                });
            }

            function refreshSectionBadges() {
                document.querySelectorAll('#sectionsContainer .sec-card').forEach((c, i) => {
                    const b = document.getElementById('secBadge-' + c.dataset.id);
                    if (b) b.textContent = i + 1;
                });
            }

            function commitSectionOrder() {
                const ids = [...document.querySelectorAll('#sectionsContainer .sec-card')]
                    .map(c => parseInt(c.dataset.id));
                Livewire.dispatch('reorderSections', {
                    ids
                });
                notify('Orden de secciones guardado');
            }

            window.toggleSectionSort = function() {
                secSortOn = !secSortOn;
                const btn = document.getElementById('sortSectionsBtn');
                const btnText = document.getElementById('sortSectionsBtnText');
                const confirm = document.getElementById('confirmSectionsBtn');
                const handles = document.querySelectorAll('.sec-handle-global');
                const cards = document.querySelectorAll('#sectionsContainer .sec-card');

                if (secSortOn) {
                    btn.classList.add('active');
                    btnText.textContent = 'Cancelar';
                    confirm.style.display = 'inline-flex';
                    handles.forEach(h => h.classList.add('visible'));
                    cards.forEach(card => {
                        const handle = card.querySelector('.sec-handle-global');
                        if (!handle) return;
                        if (!handle._mdBound) {
                            handle._mdBound = true;
                            handle.addEventListener('mousedown', () => {
                                card._handleDown = true;
                                card.setAttribute('draggable', 'true');
                            });
                            handle.addEventListener('mouseup', () => card.setAttribute('draggable',
                                'false'));
                        }
                        bindSection(card);
                    });
                } else {
                    secSortOn = false;
                    btn.classList.remove('active');
                    btnText.textContent = 'Ordenar secciones';
                    confirm.style.display = 'none';
                    handles.forEach(h => h.classList.remove('visible'));
                    cards.forEach(c => {
                        c.setAttribute('draggable', 'false');
                        c._handleDown = false;
                    });
                }
            };

            window.confirmSectionSort = function() {
                commitSectionOrder();
                secSortOn = false;
                const btn = document.getElementById('sortSectionsBtn');
                const btnText = document.getElementById('sortSectionsBtnText');
                const confirm = document.getElementById('confirmSectionsBtn');
                const handles = document.querySelectorAll('.sec-handle-global');
                const cards = document.querySelectorAll('#sectionsContainer .sec-card');
                btn.classList.remove('active');
                btnText.textContent = 'Ordenar secciones';
                confirm.style.display = 'none';
                handles.forEach(h => h.classList.remove('visible'));
                cards.forEach(c => {
                    c.setAttribute('draggable', 'false');
                    c._handleDown = false;
                });
            };

            /* ══ DETALLES ══ */
            function bindDetail(row, sectionId) {
                if (row._detBound) return;
                row._detBound = true;

                row.addEventListener('dragstart', e => {
                    if (!row._handleDown) {
                        e.preventDefault();
                        return;
                    }
                    detDragSrc = row;
                    requestAnimationFrame(() => row.classList.add('dragging'));
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', row.dataset.id);
                    e.stopPropagation();
                });

                row.addEventListener('dragend', () => {
                    row.classList.remove('dragging');
                    row._handleDown = false;
                    detDragSrc = null;
                    const list = document.getElementById('detailsList-' + sectionId);
                    list && list.querySelectorAll('.detail-row').forEach(r => r.classList.remove(
                        'drag-over-top', 'drag-over-bottom'));
                    refreshDetailNums(sectionId);
                });

                row.addEventListener('dragover', e => {
                    if (!detDragSrc || detDragSrc === row) return;
                    e.preventDefault();
                    e.stopPropagation();
                    row.classList.remove('drag-over-top', 'drag-over-bottom');
                    const mid = row.getBoundingClientRect().top + row.getBoundingClientRect().height / 2;
                    row.classList.add(e.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
                });

                row.addEventListener('dragleave', e => {
                    if (!row.contains(e.relatedTarget)) row.classList.remove('drag-over-top',
                        'drag-over-bottom');
                });

                row.addEventListener('drop', e => {
                    if (!detDragSrc || detDragSrc === row) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const list = document.getElementById('detailsList-' + sectionId);
                    const mid = row.getBoundingClientRect().top + row.getBoundingClientRect().height / 2;
                    list.insertBefore(detDragSrc, e.clientY < mid ? row : row.nextSibling);
                    row.classList.remove('drag-over-top', 'drag-over-bottom');
                    refreshDetailNums(sectionId);
                });
            }

            function refreshDetailNums(sectionId) {
                const list = document.getElementById('detailsList-' + sectionId);
                if (!list) return;
                list.querySelectorAll('.detail-row').forEach((r, i) => {
                    const n = r.querySelector('.detail-num');
                    if (n) n.textContent = i + 1;
                });
            }

            function commitDetailOrder(sectionId) {
                const list = document.getElementById('detailsList-' + sectionId);
                if (!list) return;
                const ids = [...list.querySelectorAll('.detail-row')].map(r => parseInt(r.dataset.id));
                Livewire.dispatch('reorderDetails', {
                    sectionId,
                    ids
                });
                notify('Orden de temas guardado');
            }

            window.toggleDetailSort = function(sectionId) {
                const btn = document.getElementById('sortDetailsBtn-' + sectionId);
                const confirm = document.getElementById('confirmDetailsBtn-' + sectionId);
                const confirmMobile = document.getElementById('confirmDetailsMobile-' + sectionId);
                const mobileBtn = document.getElementById('sortDetailsMobile-' + sectionId);
                const mobileBtnText = document.getElementById('sortDetailsMobileText-' + sectionId);
                const bar = document.getElementById('sortBar-' + sectionId);
                const handles = document.querySelectorAll('.detail-handle-' + sectionId);
                const rows = document.querySelectorAll('#detailsList-' + sectionId + ' .detail-row');

                if (detSortOn.has(sectionId)) {
                    // ── CANCELAR ──
                    detSortOn.delete(sectionId);
                    if (btn) {
                        btn.classList.remove('active');
                        btn.setAttribute('data-tip', 'Ordenar temas');
                    }
                    confirm && (confirm.style.display = 'none');
                    if (confirmMobile) confirmMobile.style.display = 'none';
                    if (mobileBtnText) mobileBtnText.textContent = 'Ordenar temas';
                    if (mobileBtn) mobileBtn.classList.remove('item-cancel');
                    bar && bar.classList.remove('visible');
                    handles.forEach(h => h.classList.remove('visible'));
                    rows.forEach(r => r.setAttribute('draggable', 'false'));
                } else {
                    // ── ACTIVAR ──
                    detSortOn.add(sectionId);
                    if (btn) {
                        btn.classList.add('active');
                        btn.setAttribute('data-tip', 'Cancelar orden');
                    }
                    confirm && (confirm.style.display = 'inline-flex');
                    if (confirmMobile) confirmMobile.style.display = 'flex';
                    if (mobileBtnText) mobileBtnText.textContent = 'Cancelar ordenar';
                    if (mobileBtn) mobileBtn.classList.add('item-cancel');
                    bar && bar.classList.add('visible');
                    handles.forEach(h => h.classList.add('visible'));
                    rows.forEach(row => {
                        const handle = row.querySelector('.detail-handle-' + sectionId);
                        if (handle && !handle._mdBound) {
                            handle._mdBound = true;
                            // Mouse (desktop)
                            handle.addEventListener('mousedown', () => {
                                row._handleDown = true;
                                row.setAttribute('draggable', 'true');
                            });
                            handle.addEventListener('mouseup', () => row.setAttribute('draggable',
                                'false'));
                            // Touch (móvil)
                            bindTouchDetail(handle, row, sectionId);
                        }
                        bindDetail(row, sectionId);
                    });
                }
            };

            window.confirmDetailSort = function(sectionId) {
                commitDetailOrder(sectionId);
                detSortOn.delete(sectionId);
                const btn = document.getElementById('sortDetailsBtn-' + sectionId);
                const confirm = document.getElementById('confirmDetailsBtn-' + sectionId);
                const confirmMobile = document.getElementById('confirmDetailsMobile-' + sectionId);
                const mobileBtnText = document.getElementById('sortDetailsMobileText-' + sectionId);
                const mobileBtn = document.getElementById('sortDetailsMobile-' + sectionId);
                const bar = document.getElementById('sortBar-' + sectionId);
                const handles = document.querySelectorAll('.detail-handle-' + sectionId);
                const rows = document.querySelectorAll('#detailsList-' + sectionId + ' .detail-row');
                if (btn) {
                    btn.classList.remove('active');
                    btn.setAttribute('data-tip', 'Ordenar temas');
                }
                if (mobileBtnText) mobileBtnText.textContent = 'Ordenar temas';
                if (mobileBtn) mobileBtn.classList.remove('item-cancel');
                confirm && (confirm.style.display = 'none');
                if (confirmMobile) confirmMobile.style.display = 'none';
                bar && bar.classList.remove('visible');
                handles.forEach(h => h.classList.remove('visible'));
                rows.forEach(r => r.setAttribute('draggable', 'false'));
            };

            /* ══ TOUCH DnD — MÓVIL ══════════════════════════════════
               Implementación con touchstart/touchmove/touchend para que
               el scroll no interfiera. Solo activo cuando el sort está on.
            ═══════════════════════════════════════════════════════════ */

            let touchGhost = null;
            let touchSrc = null;
            let touchType = null; // 'section' | 'detail'
            let touchSecId = null;

            function createGhost(text) {
                const g = document.createElement('div');
                g.className = 'touch-ghost';
                g.textContent = text;
                document.body.appendChild(g);
                return g;
            }

            function moveGhost(x, y) {
                if (!touchGhost) return;
                touchGhost.style.left = (x - touchGhost.offsetWidth / 2) + 'px';
                touchGhost.style.top = (y - 20) + 'px';
            }

            function removeGhost() {
                if (touchGhost) {
                    touchGhost.remove();
                    touchGhost = null;
                }
            }

            function getElementAtPoint(x, y, exclude) {
                // Ocultar ghost para poder hacer elementFromPoint limpio
                if (touchGhost) touchGhost.style.display = 'none';
                const el = document.elementFromPoint(x, y);
                if (touchGhost) touchGhost.style.display = '';
                return el;
            }

            function findAncestor(el, selector) {
                while (el) {
                    if (el.matches && el.matches(selector)) return el;
                    el = el.parentElement;
                }
                return null;
            }

            /* ── Touch para secciones ── */
            function bindTouchSection(handle, card) {
                if (handle._touchSecBound) return;
                handle._touchSecBound = true;

                handle.addEventListener('touchstart', e => {
                    if (!secSortOn) return;
                    e.preventDefault(); // ← bloquea scroll solo desde el handle
                    touchSrc = card;
                    touchType = 'section';
                    card.classList.add('dragging');
                    const titleEl = card.querySelector('.sec-title');
                    touchGhost = createGhost(titleEl ? titleEl.textContent.trim() : 'Sección');
                    const t = e.touches[0];
                    moveGhost(t.clientX, t.clientY);
                }, {
                    passive: false
                });

                handle.addEventListener('touchmove', e => {
                    if (touchType !== 'section' || !touchSrc) return;
                    e.preventDefault();
                    const t = e.touches[0];
                    moveGhost(t.clientX, t.clientY);

                    // Indicador visual de posición
                    document.querySelectorAll('#sectionsContainer .sec-card').forEach(c =>
                        c.classList.remove('drag-over-top', 'drag-over-bottom'));

                    const el = getElementAtPoint(t.clientX, t.clientY);
                    const target = findAncestor(el, '.sec-card');
                    if (target && target !== touchSrc) {
                        const rect = target.getBoundingClientRect();
                        const mid = rect.top + rect.height / 2;
                        target.classList.add(t.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
                    }
                }, {
                    passive: false
                });

                handle.addEventListener('touchend', e => {
                    if (touchType !== 'section' || !touchSrc) return;
                    const t = e.changedTouches[0];
                    removeGhost();
                    touchSrc.classList.remove('dragging');

                    const el = getElementAtPoint(t.clientX, t.clientY);
                    const target = findAncestor(el, '.sec-card');
                    if (target && target !== touchSrc) {
                        const rect = target.getBoundingClientRect();
                        const mid = rect.top + rect.height / 2;
                        const container = document.getElementById('sectionsContainer');
                        container.insertBefore(touchSrc, t.clientY < mid ? target : target.nextSibling);
                        refreshSectionBadges();
                    }

                    document.querySelectorAll('#sectionsContainer .sec-card').forEach(c =>
                        c.classList.remove('drag-over-top', 'drag-over-bottom'));

                    touchSrc = null;
                    touchType = null;
                });
            }

            /* ── Touch para detalles ── */
            function bindTouchDetail(handle, row, sectionId) {
                if (handle._touchDetBound) return;
                handle._touchDetBound = true;

                handle.addEventListener('touchstart', e => {
                    if (!detSortOn.has(sectionId)) return;
                    e.preventDefault();
                    touchSrc = row;
                    touchType = 'detail';
                    touchSecId = sectionId;
                    row.classList.add('dragging');
                    const titleEl = row.querySelector('.detail-title');
                    touchGhost = createGhost(titleEl ? titleEl.textContent.trim() : 'Tema');
                    const t = e.touches[0];
                    moveGhost(t.clientX, t.clientY);
                }, {
                    passive: false
                });

                handle.addEventListener('touchmove', e => {
                    if (touchType !== 'detail' || !touchSrc) return;
                    e.preventDefault();
                    const t = e.touches[0];
                    moveGhost(t.clientX, t.clientY);

                    const list = document.getElementById('detailsList-' + touchSecId);
                    list && list.querySelectorAll('.detail-row').forEach(r =>
                        r.classList.remove('drag-over-top', 'drag-over-bottom'));

                    const el = getElementAtPoint(t.clientX, t.clientY);
                    const target = findAncestor(el, '.detail-row');
                    if (target && target !== touchSrc) {
                        const rect = target.getBoundingClientRect();
                        const mid = rect.top + rect.height / 2;
                        target.classList.add(t.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
                    }
                }, {
                    passive: false
                });

                handle.addEventListener('touchend', e => {
                    if (touchType !== 'detail' || !touchSrc) return;
                    const t = e.changedTouches[0];
                    removeGhost();
                    touchSrc.classList.remove('dragging');

                    const el = getElementAtPoint(t.clientX, t.clientY);
                    const target = findAncestor(el, '.detail-row');
                    if (target && target !== touchSrc) {
                        const list = document.getElementById('detailsList-' + touchSecId);
                        const rect = target.getBoundingClientRect();
                        const mid = rect.top + rect.height / 2;
                        list.insertBefore(touchSrc, t.clientY < mid ? target : target.nextSibling);
                        refreshDetailNums(touchSecId);
                    }

                    const list = document.getElementById('detailsList-' + touchSecId);
                    list && list.querySelectorAll('.detail-row').forEach(r =>
                        r.classList.remove('drag-over-top', 'drag-over-bottom'));

                    touchSrc = null;
                    touchType = null;
                    touchSecId = null;
                });
            }

            /* Enganchar touch en secciones al activar sort de secciones */
            const _origToggleSecSort = window.toggleSectionSort;
            window.toggleSectionSort = function() {
                _origToggleSecSort();
                if (secSortOn) {
                    document.querySelectorAll('#sectionsContainer .sec-card').forEach(card => {
                        const handle = card.querySelector('.sec-handle-global');
                        if (handle) bindTouchSection(handle, card);
                    });
                }
            };

            function clearDropClasses() {
                document.querySelectorAll('.sec-card, .detail-row').forEach(el =>
                    el.classList.remove('drag-over-top', 'drag-over-bottom', 'dragging'));
            }

            function clearCardDrop(card) {
                card.classList.remove('drag-over-top', 'drag-over-bottom');
            }

            /* ── Reset tras re-render de Livewire ── */
            document.addEventListener('livewire:updated', () => {
                document.querySelectorAll('.sec-card').forEach(el => {
                    delete el._secBound;
                    delete el._handleDown;
                    const h = el.querySelector('.sec-handle-global');
                    if (h) delete h._mdBound;
                });
                document.querySelectorAll('.detail-row').forEach(el => {
                    delete el._detBound;
                    delete el._handleDown;
                });
                document.querySelectorAll('[class*="detail-handle-"]').forEach(h => delete h._mdBound);
            });

            document.addEventListener('DOMContentLoaded', () => {
                const c = document.getElementById('sectionsContainer');
                if (c) c.addEventListener('dragover', e => {
                    if (secDragSrc) e.preventDefault();
                });
            });

        })();
    </script>

</x-filament-panels::page>
