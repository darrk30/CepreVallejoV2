<x-filament-panels::page>
    <style>
        .ra-wrap {
            font-family: 'Handlee', cursive, sans-serif;
            color: #0c0b1a;
        }

        .dark .ra-wrap {
            color: #edecf8;
        }

        /* TOOLBAR */
        .ra-toolbar {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .ra-search-box {
            position: relative;
            min-width: 220px;
            max-width: 360px;
            flex: 1;
        }

        .ra-search-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: #8b88b0;
            pointer-events: none;
        }

        .dark .ra-search-icon {
            color: #636086;
        }

        .ra-search-input {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(99, 102, 241, 0.14);
            background: #f9f8ff;
            padding: 9px 13px 9px 38px;
            font-size: 0.83rem;
            color: #0c0b1a;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .ra-search-input::placeholder {
            color: #8b88b0;
        }

        .ra-search-input:focus {
            border-color: #5b5ef4;
            background: white;
            box-shadow: 0 0 0 3px rgba(91, 94, 244, 0.07);
        }

        .dark .ra-search-input {
            border-color: rgba(129, 140, 248, 0.12);
            background: #1b1a2e;
            color: #edecf8;
        }

        .dark .ra-search-input::placeholder {
            color: #636086;
        }

        .dark .ra-search-input:focus {
            border-color: #818cf8;
            background: #151424;
        }

        .ra-count {
            white-space: nowrap;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #8b88b0;
        }

        .dark .ra-count {
            color: #636086;
        }

        /* TABLE CARD */
        .ra-table-card {
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.12);
            background: white;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.06);
        }

        .dark .ra-table-card {
            border-color: rgba(129, 140, 248, 0.10);
            background: #151424;
        }

        .ra-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ra-table thead tr {
            border-bottom: 1px solid rgba(99, 102, 241, 0.12);
        }

        .dark .ra-table thead tr {
            border-bottom-color: rgba(129, 140, 248, 0.10);
        }

        .ra-table th {
            padding: 12px 20px;
            text-align: left;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8b88b0;
        }

        .dark .ra-table th {
            color: #636086;
        }

        .ra-table td {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.08);
        }

        .dark .ra-table td {
            border-bottom-color: rgba(129, 140, 248, 0.08);
        }

        .ra-table tbody tr {
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .ra-table tbody tr:hover {
            background: #f9f8ff;
        }

        .dark .ra-table tbody tr:hover {
            background: #1b1a2e;
        }

        .ra-td-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0c0b1a;
        }

        .dark .ra-td-name {
            color: #edecf8;
        }

        .ra-td-msg {
            font-size: 0.82rem;
            font-style: italic;
            color: #5b5875;
        }

        .dark .ra-td-msg {
            color: #9d9ac2;
        }

        .ra-td-date {
            font-size: 0.78rem;
            color: #a5a2c4;
        }

        .dark .ra-td-date {
            color: #54506e;
        }

        .ra-col-date {
            display: table-cell;
        }

        @media (max-width: 640px) {
            .ra-col-date {
                display: none;
            }
        }

        .ra-empty {
            padding: 60px 20px;
            text-align: center;
            font-size: 0.85rem;
            color: #8b88b0;
        }

        .dark .ra-empty {
            color: #636086;
        }

        /* BADGE */
        .ra-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .ra-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
        }

        .ra-badge-activo {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }

        .dark .ra-badge-activo {
            color: #34d399;
        }

        .ra-badge-activo .ra-badge-dot {
            background: #10b981;
        }

        .ra-badge-eliminado {
            background: rgba(239, 68, 68, 0.10);
            color: #ef4444;
        }

        .ra-badge-eliminado .ra-badge-dot {
            background: #ef4444;
        }

        /* PAGINATION */
        .ra-pagination {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        /* MODAL */
        .ra-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            margin: 0;
            z-index: 9000;
            background-color: rgba(12, 11, 26, 0.6);
            backdrop-filter: blur(2px);
        }

        .ra-modal-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 560px;
            max-width: 90vw;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 20px;
            background: white;
            box-shadow: 0 30px 70px rgba(12, 11, 26, 0.35);
            z-index: 9001;
        }

        .dark .ra-modal-box {
            background: #151424 !important;
        }

        .ra-modal-header {
            padding: 24px 28px 16px 28px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.12);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-shrink: 0;
        }

        .dark .ra-modal-header {
            border-bottom-color: rgba(129, 140, 248, 0.12);
        }

        .ra-modal-eyebrow {
            margin: 0 0 4px 0;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #5b5ef4;
        }

        .dark .ra-modal-eyebrow {
            color: #818cf8;
        }

        .ra-modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.3;
            color: #0c0b1a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .ra-modal-title {
            color: #edecf8;
        }

        .ra-modal-close {
            display: flex;
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: none;
            background: transparent;
            color: #8b88b0;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .ra-modal-close:hover {
            background: rgba(91, 94, 244, 0.10);
            color: #5b5ef4;
        }

        .dark .ra-modal-close {
            color: #636086;
        }

        .dark .ra-modal-close:hover {
            background: rgba(129, 140, 248, 0.10);
            color: #818cf8;
        }

        .ra-modal-body {
            padding: 20px 28px;
            overflow-y: auto;
            flex: 1 1 auto;
        }

        .ra-modal-text strong,
        .ra-modal-text b {
            font-weight: 700;
            font-style: normal;
        }

        .ra-modal-text em,
        .ra-modal-text i {
            font-style: italic;
        }

        .ra-modal-text ul {
            list-style: disc;
            padding-left: 22px;
            margin: 6px 0;
        }

        .ra-modal-text ol {
            list-style: decimal;
            padding-left: 22px;
            margin: 6px 0;
        }

        .ra-modal-text li {
            margin-bottom: 2px;
        }

        .ra-modal-text {
            margin: 0;
            white-space: pre-line;
            font-size: 0.88rem;
            font-style: italic;
            line-height: 1.8;
            color: #3b3866;
        }

        .dark .ra-modal-text {
            color: #c1bee0;
        }

        .ra-modal-footer {
            padding: 14px 28px;
            border-top: 1px solid rgba(99, 102, 241, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
            background: #f9f8ff;
        }

        .dark .ra-modal-footer {
            border-top-color: rgba(129, 140, 248, 0.12);
            background: #1b1a2e;
        }

        .ra-modal-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ra-modal-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.68rem;
            font-weight: 600;
            color: #a5a2c4;
        }

        .dark .ra-modal-date {
            color: #54506e;
        }

        .ra-modal-status {
            font-size: 0.68rem;
            font-weight: 700;
        }

        .ra-modal-status-activo {
            color: #059669;
        }

        .ra-modal-status-eliminado {
            color: #ef4444;
        }

        .ra-modal-close-btn {
            border: none;
            border-radius: 999px;
            background: #5b5ef4;
            padding: 8px 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .ra-modal-close-btn:hover {
            background: #4b4ee0;
        }

        .dark .ra-modal-close-btn {
            background: #818cf8;
        }

        .dark .ra-modal-close-btn:hover {
            background: #6f78f0;
        }
    </style>

    <div class="ra-wrap" x-data="{ modalAbierto: false, modalAlumno: '', modalTexto: '', modalFecha: '', modalEstado: '' }" @keydown.escape.window="modalAbierto = false">

        {{-- TOOLBAR ── --}}
        <div class="ra-toolbar">
            <div class="ra-search-box">
                <svg class="ra-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" class="ra-search-input" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por alumno o comentario…">
            </div>
            <span class="ra-count">
                {{ $this->getReviews()->total() }} comentario{{ $this->getReviews()->total() !== 1 ? 's' : '' }}
            </span>
        </div>

        {{-- TABLA ── --}}
        <div class="ra-table-card">
            <table class="ra-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Comentario</th>
                        <th class="ra-col-date">Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getReviews() as $review)
                        @php
                            $mensajeTexto = strip_tags($review->mensaje);
                            $mensajeCorto = \Illuminate\Support\Str::limit($mensajeTexto, 70);
                        @endphp
                        <tr wire:key="admin-review-{{ $review->id }}"
                            @click="modalAbierto = true;
                                modalAlumno = {{ \Illuminate\Support\Js::from($review->user->name ?? 'Sin nombre') }};
                                modalTexto = {{ \Illuminate\Support\Js::from(strip_tags($review->mensaje, '<strong><b><em><i><ul><ol><li><br><p>')) }};
                                modalFecha = {{ \Illuminate\Support\Js::from($review->fecha_hora->format('d/m/Y H:i')) }};
                                modalEstado = {{ \Illuminate\Support\Js::from($review->estado) }};">
                            <td class="ra-td-name">{{ $review->user->name ?? 'Sin nombre' }}</td>
                            <td class="ra-td-msg">{{ $mensajeCorto }}</td>
                            <td class="ra-td-date ra-col-date">{{ $review->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td>
                                <span
                                    class="ra-badge {{ $review->estado === 'activo' ? 'ra-badge-activo' : 'ra-badge-eliminado' }}">
                                    <span class="ra-badge-dot"></span>
                                    {{ ucfirst($review->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="ra-empty">No hay comentarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN ── --}}
        @if ($this->getReviews()->hasPages())
            <div class="ra-pagination">
                {{ $this->getReviews()->links() }}
            </div>
        @endif

        {{-- MODAL ── --}}
        <template x-teleport="body">
            <div x-show="modalAbierto" x-cloak class="ra-modal-overlay" @click.self="modalAbierto = false">
                <div x-show="modalAbierto" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="ra-modal-box">

                    <div class="ra-modal-header">
                        <div style="min-width: 0;">
                            <p class="ra-modal-eyebrow">Comentario de</p>
                            <h3 class="ra-modal-title" x-text="modalAlumno"></h3>
                        </div>
                        <button type="button" class="ra-modal-close" @click="modalAbierto = false">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="ra-modal-body">
                        <div class="ra-modal-text" x-html="modalTexto"></div>
                    </div>

                    <div class="ra-modal-footer">
                        <div class="ra-modal-meta">
                            <span class="ra-modal-date">
                                <svg width="14" height="14" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span x-text="modalFecha"></span>
                            </span>
                            <span class="ra-modal-status"
                                :class="modalEstado === 'activo' ? 'ra-modal-status-activo' : 'ra-modal-status-eliminado'"
                                x-text="modalEstado === 'activo' ? 'Activo' : 'Eliminado'"></span>
                        </div>
                        <button type="button" class="ra-modal-close-btn" @click="modalAbierto = false">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-filament-panels::page>
